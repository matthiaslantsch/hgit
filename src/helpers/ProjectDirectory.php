<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use RuntimeException;
use holonet\common\FilesystemUtils;
use holonet\hgit\models\ProjectModel;

/**
 * The ProjectDirectory class is used to wrap around a standardized project directory on the file system.
 */
class ProjectDirectory {
	/**
	 * @var string $basepath String with the base path of the project directory
	 */
	public string $basepath;

	public ProjectModel $project;

	public function __construct(string $basepath, ProjectModel $project) {
		if (!file_exists($basepath)) {
			throw new RuntimeException("Project directory '{$basepath}' does not exist");
		}

		$this->basepath = $basepath;
		$this->project = $project;
	}

	public function __toString(): string {
		return $this->basepath;
	}

	public function subpath(string ...$subpath) {
		return FilesystemUtils::filepath($this->basepath, ...$subpath);
	}
}
