<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\data;

use holonet\hgit\models\ProjectModel;
use holonet\hgit\utils\phphgit\Repository;

/**
 * Class GitContext represents a "place" in a git repository (path, refspec current branch usw...).
 */
class GitRepositoryContext {
	public ?string $branch = null;

	public function __construct(
		public ProjectModel $project,
		public Repository $repository,
		public string $ref,
		public string $path
	) {
		if (array_key_exists($this->ref, $this->repository->branches)) {
			// we ARE on a branch not in a "detached" context
			$this->branch = $this->ref;
		}
	}

	public function routeParameters(?string $ref = null, ?string $path = null): array {
		$params = array(
			'slug' => $this->project->slug,
			'repository' => basename($this->repository->path),
			'ref' => $ref
		);

		if (!empty($path) && $path !== '/' && $path !== '.') {
			$params['path'] = $path;
		}

		return $params;
	}
}
