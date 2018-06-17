<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * class file for the logic Commit class
 */

namespace holonet\hgit\helpers\phphgit\objects;

use holonet\hgit\helpers\phphgit\Change;

/**
 * The Commit class represents a git commit object
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package holonet\hgit\helpers\phphgit\objects
 */
class Commit extends GitObject {

	/**
	 * property for a commit pretty print format string
	 *   :/$/: => separator
	 *   %H => complete hash
	 *   %an => commit author name
	 *   %ar => commit ago string
	 *   %s => commit message string
	 *   %ct => commit timestamp
	 *
	 * @access public
	 * @var    string COMMITFORMAT | pretty print format for the commit
	 */
	public static $COMMITFORMAT = "%H:/$/:%an:/$/:%ar:/$/:%s:/$/:%ct";

	/**
	 * property for the commit author
	 *
	 * @access public
	 * @var    string author | the commit author
	 */
	public $author;

	/**
	 * property for the commit ago time
	 *
	 * @access public
	 * @var    string ago | the commit ago time
	 */
	public $ago;

	/**
	 * property for the commit msg
	 *
	 * @access public
	 * @var    string msg | the commit message
	 */
	public $msg;

	/**
	 * property for the commit timestamp
	 *
	 * @access public
	 * @var    string timestamp | the commit timestamp
	 */
	public $timestamp;

	/**
	 * constructor method taking the name for the branch
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | the hash of the commit
	 * @param  string author | the author of the commit
	 * @param  string ago | the ago string of the commit
	 * @param  string msg | the msg of the commit
	 * @param  string ts | the timestamp of the commit
	 */
	public function __construct($repo, $hash, $author, $ago, $msg, $ts) {
		parent::__construct($repo, $hash);
		$this->author = $author;
		$this->ago = $ago;
		$this->msg = $msg;
		$this->timestamp = $ts;
	}

	/**
	 * method for creating a commit object using a commit hash
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | the hash to find the commit with
	 * @return a newly created Commit object (of this class)
	 */
	public static function fromHash($repository, $hash) {
		$cmd = sprintf("log -1 -U %s --abbrev-commit --pretty=format:'%s'",
			$hash, self::$COMMITFORMAT
		);

		$out = $repository->execGit($cmd);
		if(strpos($out, "fatal") === 0 || strpos($out, ':/$/:') === false) {
			throw new \Exception("Could not find commit by hash {$hash}", 10);
		}

		$headline = strstr($out, "\n", true);
		$line = explode(':/$/:', $headline);

		return new Commit(
			$repository,
			trim($line[0], "'"), //-> hash
			$line[1], //-> author
			$line[2], //-> ago string
			$line[3], //-> msg
			$line[4] //-> timestamp
		);
	}

	/**
	 * wrapper method around git diff to create a diff output
	 *
	 * @access public
	 * @return string diff output
	 */
	public function getDiff() {
		return $this->execGit("diff {$this->hash}~ {$this->hash}");
	}

	/**
	 * method used to gather the details for this specific commit
	 *
	 * @access public
	 * @return array with changes that happened in this commit
	 */
	public function details() {
		$ret = [];
		$cmd = "diff-tree -r --root --no-commit-id {$this->hash}";

		foreach(explode("\n", $this->execGit($cmd)) as $line) {
			if(strlen($line) == 0) {
				continue;
			}

			$line = explode(' ', $line);
			$line[4] = explode("\t", $line[4]);
			$ret[] = new Change(
				$this->repository,
				$line[2], //-> old object blob
				$line[3], //-> new object blob
				$line[4][0], //-> change type
				$line[4][1] //-> changed file
			);
		}

		return $ret;
	}

	/**
	 * method used to get a git show output for this specific commit (either a file or a directory listing)
	 *
	 * @access public
	 * @param  string subpath | the subpath below the repository root
	 * @return either a Blob object or a Tree object (blob object anyway)
	 */
	public function getPath($subpath = "") {
		$type = $this->execGit("cat-file -t {$this->hash}:{$subpath}");
		$hash = $this->execGit("rev-parse {$this->hash}:{$subpath}");
		if($type === "blob") {
			return new Blob($this->repository, $hash, $subpath);
		} elseif($type === "tree") {
			return new Tree($this->repository, $hash, $subpath);
		} else {
			throw new \Exception("Unknown git object type '{$type}'' at {$this->hash}:{$subpath}", 100);
		}
	}

	/**
	 * small convenience function returning the object type as a string
	 *
	 * @access public
	 * @return string "commit" to identify this as a commit object
	 */
	public function type() {
		return "commit";
	}

}
