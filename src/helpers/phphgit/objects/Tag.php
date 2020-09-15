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

class Tag extends GitObject {
	public string $name;

	public function __construct(Repository $repo, string $hash, string $name) {
		parent::__construct($repo, $hash);
		$this->name = $name;
	}

	public function type(): string {
		return 'tag';
	}
}
