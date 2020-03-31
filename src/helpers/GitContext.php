<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use holonet\hgit\helpers\phphgit\Repository;

/**
 * Class GitContext represents a "place" in a git repository (path, refspec current branch usw...).
 */
class GitContext {
	/**
	 * @var string $branch Current branch for this context
	 */
	public $branch;

	/**
	 * @var Repository $gitrepo Reference to the repository object this context is in
	 */
	public $gitrepo;

	/**
	 * @var string $path Path inside the repository at the given refspec
	 */
	public $path;

	/**
	 * @var string $refspec Denotes a reference in the git repo (branch, commit, tagname usw...)
	 */
	public $refspec;

	public function __construct(Repository $gitrepo, string $refspec, string $path) {
		$this->gitrepo = $gitrepo;
		$this->refspec = $refspec;
		$this->path = $path;
		if (array_key_exists($this->refspec, $this->gitrepo->branches)) {
			//we ARE on a branch not in a "detached" context
			$this->branch = $this->refspec;
		}
	}
}
