<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the Repository class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

use RuntimeException;
use holonet\common as co;

/**
 * The Repository class is used to wrap around the wanted git repo
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\helpers\phphgit
 */
class Repository {

	/**
	 * property for the branches
	 *
	 * @access public
	 * @var    array branches | array with the branches in object form, indexed by name
	 */
	public $branches = [];

	/**
	 * property for the tags
	 *
	 * @access public
	 * @var    array tags | array with the tags in object form, indexed by name
	 */
	public $tags = [];

	/**
	 * boolean flag marking the repo as working directory or not
	 *
	 * @access public
	 * @var    boolean workingDir | flag determing wheter work tree operation should be allowed to be run on this repo
	 */
	public $workingDir = false;

	/**
	 * constructor method taking the name for the repository
	 *
	 * @access public
	 * @param  string the path to the repository
	 */
	public function __construct($path) {
		if(substr($path, -1) !== DIRECTORY_SEPARATOR) {
			$path .= DIRECTORY_SEPARATOR;
		}

		$this->path = $path;

		$bare = $this->execGit("rev-parse --is-bare-repository ");
		if(strpos($bare, "fatal: Not a git repository") !== false) {
			throw new \Exception("The given path is not a git repository", 1);
		} elseif($bare === "false") {
			$this->workingDir = true;
		}

		$revlist = $this->execGit(
			'for-each-ref --shell --format="%(refname:short) %(objectname) %(refname)"'
		);

		foreach (explode("\n", $revlist) as $line) {
			if(strlen($line) === 0) {
				continue;
			}

			$explode = explode(" ", $line);
			$name = trim($explode[0], '\'');
			$hash = trim($explode[1], '\'');
			$refname = trim($explode[2], '\'');

			if(strpos($refname, "refs/tags/") !== false) {
				$this->tags[$name] = new objects\Tag($this, $hash, $name);
			} elseif(strpos($refname, "refs/heads/") !== false) {
				$this->branches[$name] = new Branch($this, $hash, $name);
			} else {
				throw new \RuntimeException("Error listing branches and tags, unknown refname '{$refname}'", 1000);

			}
		}
	}

	/**
	 * method used to collect statistics about the repository
	 *
	 * @access public
	 * @return assiciative array with statistics about this repository
	 */
	public function getStatistics() {
		$ret = array(
			"branches" => count($this->branches),
			"tags" => count($this->tags)
		);

		$commitCount = $this->execGit("rev-list --all --count");
		if(is_numeric($commitCount)) {
			$ret["commits"] = $commitCount;
		}

		/*$tagCount = count(explode("\n", $this->execGit("show-ref --tags")));
		if(is_numeric($tagCount)) {
			$ret["tags"] = $tagCount;
		}*/


		return $ret;
	}

	/**
	 * method used to get a git show output for this specific commit (either a file or a directory listing)
	 *
	 * @access public
	 * @param  string refspec | the refspec to look for the path at
	 * @param  string subpath | the subpath below the repository root
	 * @return either a Blob object or a Tree object (blob object anyway)
	 */
	public function getPathAtRef($refspec = "master", $subpath = "") {
		$type = $this->execGit("cat-file -t {$refspec}:{$subpath}");
		if(strpos($type, "fatal") === 0) {
			return false;
		}

		$hash = $this->execGit("rev-parse {$refspec}:{$subpath}");
		if($type === "blob") {
			return new objects\Blob($this, $hash, $subpath);
		} elseif($type === "tree") {
			return new objects\Tree($this, $hash, $subpath);
		} else {
			throw new \Exception("Unknown git object type '{$type}'' at {$refspec}:{$subpath}", 100);
		}
	}

	/**
	 * wrapper method passing the command to the Hgit class
	 * with the path of the repository
	 *
	 * @access public
	 * @param  string cmd | the git subcommand to execute
	 * @return the output of the git command
	 */
	public function execGit($cmd) {
		return PHPHGit::execGit($cmd, $this->path);
	}

	/**
	 * static method used to clone a git repository into a working dir
	 * and then returning a Repository object on that new working copy
	 * if no target directory is given, a directory in sys_temp_dir is created
	 *
	 * @access public
	 * @param  string url | the url of the git repository to clone
	 * @param  string path | the path for the cloned repository
	 * @return Repository object of the new working copy
	 */
	public static function clone($url, $path = null) {
		if($path === null) {
			$path = sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($url);
		}

		$error = PHPHGit::execGit("clone {$url} {$path}");
		try {
			return new Repository($path);
		} catch (\Exception $e) {
			throw new \Exception("Error clone repository: {$error}", 100);
		}
	}

	/**
	 * method called on a working tree to add a file to the git index
	 * if the file doesn't exist, it will be created and then added
	 *
	 * @access public
	 * @param  string filename | the filename for the file to add/create and add
	 * @param  string content | a string with content for the file to be set
	 * @return the output of the git command
	 */
	public function add($filename, $content = null) {
		$fullpath = $this->path.$filename;
		if(!file_exists($fullpath)) {
			touch($fullpath);
		}

		if($content !== null) {
			file_put_contents($fullpath, $content);
		}

		return $this->execGit("add {$filename}");
	}

	/**
	 * method called on a working tree to checkout a branch
	 * use the boolean flag to create a new branch
	 *
	 * @access public
	 * @param  string branch | the name of the branch to be checked out
	 * @param  boolean create | flag determing wheter a -b flag should be added
	 * @return the output of the git command
	 */
	public function checkout($branch, $create = false) {
		$cmd = "checkout";
		if(!isset($this->branches[$branch])) {
			 if($create === true) {
				$cmd .= " -b";
			}
		}

		return $this->execGit("{$cmd} {$branch}");
	}

	/**
	 * method called on a working tree to commit staged changes
	 *
	 * @access public
	 * @param  string msg | the message for the commit
	 * @return the output of the git command
	 */
	public function commit($msg) {
		$msg = escapeshellarg($msg);
		return $this->execGit("commit -m {$msg}");
	}

	/**
	 * get a commit in this repository by its hash
	 *
	 * @access public
	 * @param  string $hash The hash of the commit to find
	 * @return The commit object identified by the hash
	 */
	public function commitByHash(string $hash) {
		return objects\Commit::fromHash($this, $hash);
	}

	/**
	 * method called to run a git http-backend command on the repository
	 *
	 * @access public
	 * @param  array $environment An array with environment values for the git process
	 * @param  string $stdin Standard input that is to be written to the git backend
	 * @return the output of the git http-backend command
	 */
	public function httpBackend(array $environment, string $stdin) {
		// start program
		$process = proc_open(
			co\registry('gitExe').' http-backend',
			array(
				0 => array('pipe', 'r'),
				1 => array('pipe', 'w'),
				2 => array('pipe', 'r'),
			),
			$pipes,
			null,
			$environment,
			array('bypass_shell' => true) // bypass_shell = windows-only flag
		);

		if (!is_resource($process)) {
			throw new RuntimeException('Error while executing "git http-backend" process');
		}

		// write client's post data to program
		fwrite($pipes[0], $stdin);
		fclose($pipes[0]);
		$output = stream_get_contents($pipes[1]);
		$errorOut = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		if(proc_close($process) !== 0) {
			throw new RuntimeException('An error occured and the "git http-backend" process failed: '.var_export($errorOut, true));
		}
		return $output;
	}

}
