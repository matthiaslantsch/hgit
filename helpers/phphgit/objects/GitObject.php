<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * class file for the git object base class
 * is inherited by git object classes commit, blob, tree, tag
 */

namespace holonet\hgit\helpers\phphgit\objects;

use holonet\hgit\helpers\phphgit\Repository;

/**
 * abstract base class to logically represent a git object
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package holonet\hgit\helpers\phphgit\objects
 */
abstract class GitObject {

	/**
	 * property containing a reference to the repository
	 * object this git object belongs to
	 *
	 * @access public
	 * @var    Repository repository | the opened git repository object
	 */
	 public $repository;

	/**
	 * property containing the git hash referencing this object
	 *
	 * @access public
	 * @var    string hash | the git hash representing the object
	 */
	 public $hash;

	/**
	 * constructor method taking the git hash as an argument
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | hash representing this object
	 */
	public function __construct(Repository $repo, $hash) {
		$this->repository = $repo;
		$this->hash = $hash;
	}

	/**
	 * wrapper method passing the command to the Hgit class
	 * to the repository object gitexec method
	 *
	 * @access public
	 * @param  string cmd | the git subcommand to execute
	 * @return the output of the git command
	 */
	public function execGit($cmd) {
		return $this->repository->execGit($cmd);
	}

	/**
	 * Return a string identifier for the object type
	 */
	public abstract function type();

}
