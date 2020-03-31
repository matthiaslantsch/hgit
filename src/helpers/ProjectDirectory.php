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
	public $basepath;

	/**
	 * @var Projectmodel $project ProjectModel instance of the project this directory belongs to
	 */
	public $project;

	/**
	 * @param string $basepath String with the base path of the project directory
	 * @param ProjectModel $project Project model instance that the directory will belong to
	 */
	public function __construct(string $basepath, ProjectModel $project) {
		if (!file_exists($basepath)) {
			throw new RuntimeException("Project directory '{$basepath}' does not exist");
		}

		$this->basepath = $basepath;
		$this->project = $project;
	}

	/**
	 * @return string base path for this project directory
	 */
	public function __toString(): string {
		return $this->basepath;
	}

	/**
	 * Get a path below the the project path.
	 * @param string ...$subpath Sub paths as parts of the final path
	 * @return string with an absolute path
	 */
	public function subpath(string ...$subpath) {
		return FilesystemUtils::filepath($this->basepath, ...$subpath);
	}
}
