<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\data\cfg;

use holonet\common\verifier\rules\filesystem\Directory;
use holonet\common\verifier\rules\filesystem\Writable;
use holonet\hgit\models\ProjectModel;

class ProjectDirectory {
	public function __construct(
		#[Directory(true)]
		#[Writable]
		public string $projectRoot
	) {}

	public function base(ProjectModel $project): string {
		return implode('/', array($this->projectRoot, $project->slug));
	}

	public function mainRepo(ProjectModel $project): string {
		return $this->repo($project, "{$project->slug}.git");
	}

	/**
	 * @param ProjectModel $projectModel Project model to notify a download on
	 * @param string $version String with the full version that was installed
	 */
	public function notifyDownload(ProjectModel $projectModel, string $version): void {
		file_put_contents(implode('/', array($this->base($projectModel), 'downloads')), "{$version}\n", \FILE_APPEND);
	}

	public function repo(ProjectModel $project, string $repo): string {
		return implode('/', array($this->projectRoot, $project->slug, 'REPO', $repo));
	}
}
