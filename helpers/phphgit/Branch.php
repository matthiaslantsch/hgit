<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the Branch class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

/**
 * The Branch class logically represents a branch on a git repository
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\hgit\helpers\phphgit
 */
class Branch extends objects\GitObject {

	/**
	 * property for the branch name
	 *
	 * @access public
	 * @var    string name | the branch name
	 */
	public $name;

	/**
	 * constructor method taking the name for the branch
	 * in addition to standard git object parameters
	 * (the repository object and a hash)
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | hash representing this object
	 * @param  string name | the name of this branch on the repository
	 */
	public function __construct(Repository $repo, $hash, $name) {
		//the "hash" in a branch sense is not an object hash, but the object
		//hash of the HEAD commit
		parent::__construct($repo, $hash);
		$this->name = $name;
	}

	/**
	 * method collecting history information about either the
	 * whole repo on a branch or a subpath (can even be a file)
	 *
	 * @access public
	 * @param  string path | subpath inside the repo (empty for branch log)
	 */
	public function getHistory($path = "") {
		$ret = [];
		$cmd = "log --abbrev-commit --pretty=format:'".objects\Commit::$COMMITFORMAT."' {$this->name} --";

		foreach (explode("\n", $this->execGit($cmd)) as $line) {
			if(strpos($line, ':/$/:') !== false) {
				$line = explode(':/$/:', $line);
				$ret[] = new objects\Commit(
					$this->repository,
					trim($line[0], "'"), //-> hash
					$line[1], //-> author
					$line[2], //-> ago string
					$line[3], //-> msg
					$line[4] //-> timestamp
				);
			}
		}

		return $ret;
	}

	/**
	 * getter function returning a Commit object for the current HEAD commit
	 *
	 * @access 	public
	 * @return  Commit object for the current head
	 */
	public function getHEAD() {
		return objects\Commit::fromHash($this->repository, $this->hash);
	}

	/**
	 * small convenience function returning the object type as a string
	 *
	 * @access public
	 * @return string "branch" to identify this as a branch object
	 */
	public function type() {
		return "branch";
	}

}
