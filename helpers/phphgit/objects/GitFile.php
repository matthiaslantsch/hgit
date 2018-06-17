<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * class file for the logic GitFile class
 */

namespace holonet\hgit\helpers\phphgit\objects;

/**
 * The GitFile class represents a git file (tree/blob)
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package holonet\hgit\helpers\phphgit\objects
 */
abstract class GitFile extends GitObject {

	/**
	 * property for the file name relative to the repo root
	 *
	 * @access public
	 * @var    string name | the file name
	 */
	public $name;

	/**
	 * property for an array of objects contained inside the file
	 *
	 * @access protected
	 * @var    mixed either array of git objects contained inside the file or binary string (content/tree listing)
	 */
	protected $content;

	/**
	 * property for a commit object for the commit that last changed this file
	 *
	 * @access protected
	 * @var    Commit object | commit object for the commit that last changed this file
	 */
	protected $lastcommit = false;

	/**
	 * constructor method taking the name for the file
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | the hash of the file
	 * @param  string filename | the name of the file (tree/blob)
	 */
	public function __construct($repo, $hash, $filename) {
		parent::__construct($repo, $hash);
		$this->name = $filename;
	}

	/**
	 * small function getting the last commit a file/tree was changed
	 *
	 * @access public
	 * @return Commit object when the file was last changed
	 */
	public function lastCommit() {
		if($this->lastcommit === false) {
			$cmd = "log -n 1 --abbrev-commit --pretty=format:'".Commit::$COMMITFORMAT."' -- {$this->name}";
			$out = $this->execGit($cmd);
			if(strpos($out, ':/$/:') === false) {
				return null;
			}

			$out = explode(':/$/:', $out);
			$this->lastcommit = new Commit(
				$this->repository,
				trim($out[0], "'"), //-> hash
				$out[1], //-> author
				$out[2], //-> ago string
				$out[3], //-> msg
				$out[4] //-> timestamp
			);
		}

		return $this->lastcommit;
	}

	/**
	 * small function to get the content of the file
	 */
	public abstract function getContent();

}
