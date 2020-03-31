<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit\objects;

use holonet\hgit\helpers\phphgit\Repository;

/**
 * abstract base class to logically represent a git object.
 */
abstract class GitObject {
	/**
	 * @var string $hash The git hash representing the object
	 */
	public $hash;

	/**
	 * @var Repository $repository Reference to the repository object this git object belongs to
	 */
	public $repository;

	/**
	 * @param Repository $repo Reference to the opened git repository
	 * @param string $hash Hash representing this object
	 */
	public function __construct(Repository $repo, string $hash) {
		$this->repository = $repo;
		$this->hash = $hash;
	}

	/**
	 * wrapper method passing the command to the repository.
	 * @param string $cmd The git subcommand to execute
	 * @return string the output of the git command
	 */
	public function execGit($cmd): string {
		return $this->repository->execGit($cmd);
	}

	/**
	 * Return a string identifier for the object type.
	 */
	abstract public function type(): string;
}
