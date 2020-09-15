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
	public string $hash;

	public Repository $repository;

	public function __construct(Repository $repo, string $hash) {
		$this->repository = $repo;
		$this->hash = $hash;
	}

	public function execGit($cmd): string {
		return $this->repository->execGit($cmd);
	}

	abstract public function type(): string;
}
