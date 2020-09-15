<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

use LogicException;
use RuntimeException;
use holonet\common\FilesystemUtils;
use holonet\hgit\services\GitService;
use holonet\hgit\helpers\phphgit\objects\Tag;
use holonet\hgit\helpers\phphgit\objects\Blob;
use holonet\hgit\helpers\phphgit\objects\Commit;
use holonet\hgit\helpers\phphgit\objects\GitFile;

class Repository {
	/**
	 * @var Branch[] $branches Array with the branches in object form, indexed by name
	 */
	public array $branches = array();

	/**
	 * @var string $path The path to the repository on the filesystem
	 */
	public string $path;

	/**
	 * @var Tag[] $tags Array with the tags in object form, indexed by name
	 */
	public array $tags = array();

	/**
	 * @var bool $workingDir Flag indicating whether work tree operation should be allowed to be run on this repo
	 */
	public bool $workingDir = false;

	private GitService $gitservice;

	public function __construct(GitService $service, string $path) {
		$this->gitservice = $service;
		$this->path = FilesystemUtils::dirpath($path);

		$bare = $this->execGit('rev-parse --is-bare-repository');
		if (mb_strpos($bare, 'fatal: Not a git repository') !== false) {
			throw new RuntimeException('The given path is not a git repository');
		}
		if ($bare === 'false') {
			$this->workingDir = true;
		}

		$revlist = $this->execGit(
			'for-each-ref --shell --format="%(refname:short) %(objectname) %(refname)"'
		);

		foreach (explode("\n", $revlist) as $line) {
			if (mb_strlen($line) === 0) {
				continue;
			}

			$explode = explode(' ', $line);
			$name = trim($explode[0], '\'');
			$hash = trim($explode[1], '\'');
			$refname = trim($explode[2], '\'');

			if (mb_strpos($refname, 'refs/tags/') !== false) {
				$this->tags[$name] = new objects\Tag($this, $hash, $name);
			} elseif (mb_strpos($refname, 'refs/heads/') !== false) {
				$this->branches[$name] = new Branch($this, $hash, $name);
			} else {
				throw new RuntimeException("Error listing branches and tags, unknown refname '{$refname}'");
			}
		}
	}

	/**
	 * method called on a working tree to add a file to the git index
	 * if the file doesn't exist, it will be created and then added.
	 * @param string $filename The filename for the file to add/create and add
	 * @param string $content A string with content for the file to be set
	 * @return string the output of the git add command
	 */
	public function add(string $filename, ?string $content = null): string {
		if (!$this->workingDir) {
			throw new LogicException("The operation 'git add' can only be run on a work tree");
		}
		$fullpath = $this->path.$filename;
		if (!file_exists($fullpath)) {
			touch($fullpath);
		}

		if ($content !== null) {
			file_put_contents($fullpath, $content);
		}

		return $this->execGit("add {$filename}");
	}

	/**
	 * method called on a working tree to checkout a branch
	 * use the boolean flag to create a new branch.
	 * @param string $branch The name of the branch to be checked out
	 * @param bool $create Flag indicating whether a -b flag should be added
	 * @return string the output of the git checkout command
	 */
	public function checkout(string $branch, bool $create = false): string {
		if (!$this->workingDir) {
			throw new LogicException("The operation 'git checkout' can only be run on a work tree");
		}
		$cmd = 'checkout';
		if (!isset($this->branches[$branch]) && $create === true) {
			$cmd .= ' -b';
		}

		return $this->execGit("{$cmd} {$branch}");
	}

	/**
	 * method called on a working tree to commit staged changes.
	 * @param string $msg The message for the commit
	 * @return string the output of the git commit command
	 */
	public function commit(string $msg): string {
		if (!$this->workingDir) {
			throw new LogicException("The operation 'git commit' can only be run on a work tree");
		}
		$msg = escapeshellarg($msg);

		return $this->execGit("commit -m {$msg}");
	}

	public function commitByHash(string $hash): Commit {
		return objects\Commit::fromHash($this, $hash);
	}

	/**
	 * wrapper method passing the command to the git service
	 * with the path of the repository.
	 * @param string $cmd The git subcommand to execute
	 * @param bool $ignoreFailure Flag allowing to just return anyway command failure
	 * @return string the output of the git command
	 */
	public function execGit(string $cmd, bool $ignoreFailure = false): string {
		return $this->gitservice->execGit($cmd, $this->path, $ignoreFailure);
	}

	/**
	 * method used to get a git show output for this specific commit (either a file or a directory listing).
	 * @param string $refspec The refspec to look for the path at
	 * @param string $subpath The subpath below the repository root
	 * @return GitFile|null either a Blob object or a Tree object or null on failure
	 */
	public function getPathAtRef(string $refspec = 'master', string $subpath = ''): ?GitFile {
		$type = $this->execGit("cat-file -t {$refspec}:{$subpath}", true);

		if (mb_strpos($type, 'fatal') === 0) {
			return null;
		}

		$hash = $this->execGit("rev-parse {$refspec}:{$subpath}");
		if ($type === 'blob') {
			return new objects\Blob($this, $hash, $subpath);
		}
		if ($type === 'tree') {
			return new objects\Tree($this, $hash, $subpath);
		}

		throw new RuntimeException("Unknown git object type '{$type}' at '{$refspec}:{$subpath}'");
	}

	/**
	 * @return array with statistics about this repository
	 */
	public function getStatistics(): array {
		$ret = array(
			'branches' => count($this->branches),
			'tags' => count($this->tags)
		);

		$commitCount = $this->execGit('rev-list --all --count');
		if (is_numeric($commitCount)) {
			$ret['commits'] = $commitCount;
		}

		return $ret;
	}

	/**
	 * method called to run a git http-backend command on the repository.
	 * @param array $environment An array with environment values for the git process
	 * @param string $stdin Standard input that is to be written to the git backend
	 * @return string with the output of the git http-backend command
	 */
	public function httpBackend(array $environment, string $stdin): string {
		// start program
		$process = proc_open(
			"\"{$this->gitservice->gitExe}\" http-backend",
			array(
				0 => array('pipe', 'r'),
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w'),
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
		$exitcode = proc_close($process);

		if ($exitcode !== 0) {
			throw new RuntimeException("An error occurred and the 'git http-backend' process failed({$exitcode}): ".var_export($errorOut, true));
		}

		return $output;
	}
}
