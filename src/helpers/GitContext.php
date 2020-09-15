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
	public ?string $branch = null;

	public Repository $gitrepo;

	public string $path;

	public string $refspec;

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
