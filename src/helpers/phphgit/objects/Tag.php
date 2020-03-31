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
 * The Tag class represents a git tag in the repo.
 */
class Tag extends GitObject {
	/**
	 * @var string $name The tag name
	 */
	public $name;

	/**
	 * @param Repository $repo object of an open git repository
	 * @param string $hash The hash of the tag
	 * @param string $name The name of the tag
	 */
	public function __construct(Repository $repo, string $hash, string $name) {
		parent::__construct($repo, $hash);
		$this->name = $name;
	}

	/**
	 * @return string "tag" to identify this as a tag object
	 */
	public function type(): string {
		return 'tag';
	}
}
