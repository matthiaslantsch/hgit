<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use Exception;
use RuntimeException;
use FilesystemIterator;
use holonet\holofw\Context;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use holonet\common\FilesystemUtils;
use holonet\hgit\models\ProjectModel;
use holonet\common\collection\Registry;
use holonet\hgit\helpers\phphgit\Repository;
use holonet\common\error\BadEnvironmentException;

/**
 * ProjectDirectoryService is meant to bridge between the filesystem project directories and the db project entries.
 */
class ProjectDirectoryService {
	/**
	 * @var Context $di_context The current fw application context
	 */
	public $di_context;

	/**
	 * @var GitService $di_gitservice Dependency injected git command service
	 */
	public $di_gitservice;

	/**
	 * @var Registry $di_registry Global config values registry
	 */
	public $di_registry;

	/**
	 * @var string $projectDir The root directory for all projects
	 */
	private $projectDir;

	/**
	 * function creating a new project directory according to standards.
	 * @param ProjectModel $project Project model instance that the directory will belong to
	 * @throws Exception if a filesystem operation failed
	 */
	public function create($project): void {
		$basepath = FilesystemUtils::dirpath($this->projectDir, $project->slugname());
		if (file_exists($basepath)) {
			throw new RuntimeException("Project directory '{$basepath}' already exists");
		}

		$tempnewdir = sys_get_temp_dir().DIRECTORY_SEPARATOR.$project->slugname().'_temp';
		if (file_exists($tempnewdir)) {
			FilesystemUtils::rrmdir($tempnewdir);
		}

		mkdir($tempnewdir);

		//create git repository folder
		mkdir($tempnewdir.DIRECTORY_SEPARATOR.'REPO');
		$gitRepo = $tempnewdir.DIRECTORY_SEPARATOR.'REPO'.DIRECTORY_SEPARATOR.$project->slugname().'.git';

		//create the main git repository
		$tempdir = sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($gitRepo);
		FilesystemUtils::rrmdir($tempdir);
		$this->di_gitservice->execGit("init --bare {$gitRepo}");

		//clone a working copy into a temporary folder
		$repo = $this->di_gitservice->clone($gitRepo);
		$repo->execGit("config user.name 'hgit daemon'");
		$repo->execGit("config user.email 'hgitdaemon@localhost'");
		$repo->add('.gitignore');
		$repo->commit('Create .gitignore file in master branch');
		$repo->execGit('push --set-upstream origin master');

		//create wiki branch
		$repo->checkout('wiki', true);
		$repo->add('readme.md', "{$project->description}\nDocumentation\n");
		$repo->commit('Create readme file in wiki branch');
		$repo->execGit('push --set-upstream origin wiki');
		$repo->checkout('master');

		//create developement branch
		$repo->checkout('develop', true);
		$repo->add('readme.md', "{$project->description}");
		$repo->commit('Create readme file in develop branch');
		$repo->execGit('push --set-upstream origin develop');

		//delete the temporary working directory
		FilesystemUtils::rrmdir($repo->path);

		//"commit" the newly created folder structure to the project root
		rename($tempnewdir, $basepath);
	}

	/**
	 * @param ProjectDirectory $projectDirectory The project directory we are working with
	 * @return array associative with statistics about the given project
	 */
	public function getStatistics(ProjectDirectory $projectDirectory) {
		//get the size of the project directory
		$dirSize = static function ($path): int {
			$bytestotal = 0;
			$path = realpath($path);
			if ($path !== false && $path !== '' && file_exists($path)) {
				foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
					$bytestotal += $object->getSize();
				}
			}

			return $bytestotal;
		};

		$bytes = $dirSize($projectDirectory);
		$sz = 'BKMGTP';
		$factor = intval((strlen($bytes) - 1) / 3);
		/** @psalm-suppress InvalidArrayOffset */
		$ret = array(
			'maintainer' => $projectDirectory->project->user->nick,
			'size' => sprintf('%.2f', $bytes / 1024 * $factor).@$sz[$factor]
		);

		$repo = $this->gitRepo($projectDirectory);

		return array_merge($ret, $repo->getStatistics());
	}

	/**
	 * @param ProjectDirectory $projectDir The project directory to open
	 * @return Repository git repository object for a given project directory
	 */
	public function gitRepo(ProjectDirectory $projectDir): Repository {
		return $this->di_gitservice->accessRepository($projectDir->subpath('REPO', "{$projectDir->project->slugname()}.git"));
	}

	/**
	 * di initialising function getting the project root directory and falling back
	 * to defaults if it wasn't configured.
	 * @throws BadEnvironmentException if the project root directory does not exist
	 */
	public function init(): void {
		$rootpth = $this->di_registry->get('projectRoot');
		if ($rootpth === null) {
			//default to var/projects/
			$root = $this->di_context->varPath('projects');
			if (!file_exists($root)) {
				mkdir($root, 0755);
			}
		} else {
			$root = realpath($rootpth);
		}

		if ($root === false) {
			throw new BadEnvironmentException("Project root directory '{$rootpth}' does not exist");
		}
		$this->projectDir = $root;
	}

	/**
	 * @param ProjectModel $projectModel Project model to notify a download on
	 * @param string $version String with the full version that was installed
	 */
	public function notifyDownload(ProjectModel $projectModel, string $version): void {
		file_put_contents($this->projectDirectory($projectModel)->subpath('downloads'), "{$version}\n", FILE_APPEND);
	}

	/**
	 * @param ProjectModel $project The project model to get the Directory object for
	 * @return ProjectDirectory instance wrapping around said projects data on the filesystem
	 */
	public function projectDirectory(ProjectModel $project): ProjectDirectory {
		return new ProjectDirectory(
			FilesystemUtils::dirpath($this->projectDir, $project->slugname()),
			$project
		);
	}
}
