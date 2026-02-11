<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\actions;

use holonet\common\di\autowire\attribute\ConfigItem;
use function holonet\common\dir_path;
use holonet\common\FilesystemUtils;
use holonet\common\verifier\rules\filesystem\Directory;
use holonet\hgit\data\cfg\ProjectDirectory;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\services\GitService;
use holonet\holofw\Context;
use RuntimeException;

class CreateProjectDirectoryAction {
	public function __construct(
		protected Context $context,
		#[ConfigItem('project_root')]
		protected ProjectDirectory $projectDirectory,
		protected GitService $gitService,
	) {}

	public function execute(ProjectModel $project): void {
		try {
			FilesystemUtils::dirShouldExist($this->context->varPath('tmp'));
			$basePath = FilesystemUtils::dirpath($this->projectDirectory->base($project));
			if (file_exists($basePath)) {
				throw new RuntimeException("Project directory '{$basePath}' already exists");
			}

			$tempNewDir = $this->context->varPath('tmp', $project->slug.'_temp');
			if (file_exists($tempNewDir)) {
				FilesystemUtils::rrmdir($tempNewDir);
			}

			// create git repository folder
			$gitRepo = dir_path($tempNewDir, 'REPO', "{$project->slug}.git");
			if (!mkdir(dirname($gitRepo), recursive: true)) {
				throw new RuntimeException(sprintf('Failed to create directory \'%s\'', dirname($gitRepo)));
			}

			// create the main git repository
			$this->gitService->execGit("init --bare {$gitRepo}");

			// clone a working copy into a temporary folder
			$tempGitDir = $this->context->varPath('tmp', basename($gitRepo));
			FilesystemUtils::rrmdir($tempGitDir);
			$repo = $this->gitService->clone($gitRepo, $tempGitDir);
			$repo->execGit("config user.name {$project->maintainer->get()->username}");
			$email = $project->maintainer->get()->email ?? 'hgitdaemon@localhost';
			$repo->execGit("config user.email {$email}");
			$repo->add('.gitignore');
			$repo->commit('Create .gitignore file in master branch');
			$repo->execGit('push --set-upstream origin master');

			// create development branch
			$repo->checkout('develop', true);
			$repo->add('readme.md', "{$project->description}");
			$repo->commit('Create readme file in develop branch');
			$repo->execGit('push --set-upstream origin develop');

			// delete the temporary working directory
			FilesystemUtils::rrmdir($tempGitDir);

			// "commit" the newly created folder structure to the project root
			if (!rename($tempNewDir, "{$this->projectDirectory->projectRoot}/{$project->slug}")) {
				throw new RuntimeException("Failed to move '{$tempNewDir}'");
			}
		} finally {
			FilesystemUtils::rrmdir($this->context->varPath('tmp'));
		}

	}
}
