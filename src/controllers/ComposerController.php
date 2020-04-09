<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\hgit\helpers\GitService;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\enum\ProjectType;
use holonet\hgit\helpers\ProjectDirectoryService;

/**
 * ComposerController exposing that is supposed to communicate with
 * a composer client and advertise the php library projects.
 */
class ComposerController extends HgitControllerBase {
	/**
	 * @var string[] DEV_VERSION_BRANCH_NAMES Names of branches to advertise as dev- versions
	 */
	public const DEV_VERSION_BRANCH_NAMES = array('master', 'develop');

	/**
	 * @var ProjectDirectoryService $di_directoryService Dependency injected project directory filesystem service
	 */
	public $di_directoryService;

	/**
	 * @var GitService $di_gitservice Dependency injected git command service
	 */
	public $di_gitservice;

	/**
	 * GET /composer/packages.json
	 * return a json list with information about this composer backend.
	 */
	public function info(): void {
		$packages = array();
		foreach ($this->getVisibleProjects(ProjectType::PHP_LIBRARY()) as $phplib) {
			$repo = $this->di_directoryService->gitRepo(
				$this->di_directoryService->projectDirectory($phplib)
			);

			$versionRefs = array_merge(
				static::DEV_VERSION_BRANCH_NAMES,
				array_column($repo->tags, 'name')
			);
			$versions = array();
			foreach ($versionRefs as $ref) {
				$atRefComposeJson = $repo->getPathAtRef('develop', 'composer.json');
				if ($atRefComposeJson !== null) {
					$atRefComposeJson = json_decode($atRefComposeJson->getContent(), true);
					$atRefComposeJson['source'] = array(
						'type' => 'git',
						'url' => "{$this->request->getSchemeAndHttpHost()}{$this::linkInternal("{$phplib->slugname()}/repo/"."{$phplib->slugname()}.git")}",
						'reference' => $ref
					);

					if (in_array($ref, static::DEV_VERSION_BRANCH_NAMES)) {
						//prefix with dev- composer version prefix
						$ref = "dev-{$ref}";
					}

					$atRefComposeJson['version'] = $ref;

					$versions[$ref] = $atRefComposeJson;
				}
			}
			if (!empty($versions)) {
				$packages[$phplib->name] = $versions;
			}
		}
		$this->view->set('packages', $packages);
		$this->view->set('notify', '/composer/notify/%package%');
		$this->view->set('notify-batch', '/composer/notify/');
		//$this->view->set("search", "/composer/search.json?q=%query%&type=%type%");

		$this->respondTo('json');
	}

	/**
	 * POST /composer/notify/[projectName:?s]
	 * backend for collecting installation data from the composer clients.
	 * @param string $packageName Optional parameter with the package name that was installed
	 */
	public function notify($packageName): void {
		/** @psalm-suppress PossiblyInvalidArgument */
		$info = json_decode($this->request->getContent(), true);
		foreach ($info['downloads'] as $notify) {
			/** @var ProjectModel $project */
			$project = $this->di_repo->get(ProjectModel::class, array('name' => $notify['name']));
			if ($project !== null) {
				$this->di_directoryService->notifyDownload($project, $notify['version']);
			} else {
				$this->notFound("Could not find installed package '{$notify['name']}'");

				return;
			}
		}
	}

	/**
	 * GET /composer/search.json?q=%query%&type=%type%
	 * a search interface for the composer client.
	 */
	public function search(): void {
		$this->notFound('Not yet implemented');

		//@TODO implement the composer type parameter
//		$query = strip_tags($this->request->query->get("query"));
//		$options = array(
//			"projectType" => ProjectTypeModel::get(array("name" => "php library")),
//			"name[~]" => $query
//		);
//		$packages = ProjectModel::select($options);
//		die(var_dump($packages));
	}
}
