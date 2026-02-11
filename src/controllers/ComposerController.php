<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\activerecord\Database;
use holonet\activerecord\ModelRepository;
use holonet\common\collection\Registry;
use holonet\common\di\autowire\attribute\ConfigItem;
use holonet\hgit\data\cfg\ProjectDirectory;
use holonet\hgit\models\enum\ProjectType;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\services\GitService;
use holonet\hgit\utils\VisibleProjectsOptionsTrait;
use holonet\holofw\Application;
use holonet\holofw\Context;
use holonet\holofw\Controller;
use holonet\holofw\Request;
use holonet\holofw\view\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/composer')]
class ComposerController extends Controller {
	use VisibleProjectsOptionsTrait;

	/**
	 * @var string[] DEV_VERSION_BRANCH_NAMES Names of branches to advertise as dev- versions
	 */
	public const array DEV_VERSION_BRANCH_NAMES = array('master', 'develop');

	public function __construct(
		Request $request,
		string $method,
		Application $app,
		Database $database,
		Context $context,
		Registry $registry,
		View $view,
		ModelRepository $repo,
		LoggerInterface $logger,
		protected GitService $gitService,
		#[ConfigItem('project_root')] protected ProjectDirectory $projectDirectory
	) {
		parent::__construct($request, $method, $app, $database, $context, $registry, $view, $repo, $logger);
	}

	/**
	 * return a json list with information about this composer backend.
	 */
	#[Route('/packages.json', methods: array('GET'))]
	public function info(): void {
		$packages = array();
		$projects = $this->repo->select(ProjectModel::class, $this->projectIndexOptions(ProjectType::PHP_LIBRARY));

		foreach ($projects as $library) {
			$repo = $this->gitService->accessRepository(
				$this->projectDirectory->mainRepo($library)
			);

			$versionRefs = array_merge(
				static::DEV_VERSION_BRANCH_NAMES,
				array_column($repo->tags, 'name')
			);
			$versions = array();
			foreach ($versionRefs as $ref) {
				$atRefComposeJson = $repo->getPathAtRef($ref, 'composer.json');
				if ($atRefComposeJson !== null) {
					$atRefComposeJson = json_decode($atRefComposeJson->getContent(), true);
					$atRefComposeJson['source'] = array(
						'type' => 'git',
						'url' => "{$this->request->getSchemeAndHttpHost()}{$this::linkInternal("projects/{$library->slug}/repo/{$library->slug}.git")}",
						'reference' => $ref
					);

					if (in_array($ref, static::DEV_VERSION_BRANCH_NAMES)) {
						// prefix with dev- composer version prefix
						$ref = "dev-{$ref}";
					}

					$atRefComposeJson['version'] = $ref;

					$versions[$ref] = $atRefComposeJson;
				}
			}
			if (!empty($versions)) {
				$packages[$library->name] = $versions;
			}
		}
		$this->share('packages', $packages);
		$this->share('notify', '/composer/notify/%package%');
		$this->share('notify-batch', '/composer/notify/');
		// $this->view->set("search", "/composer/search.json?q=%query%&type=%type%");

		$this->offerJson();
	}

	#[Route('/notify/{?slug}', methods: array('POST'))]
	public function notify(): void {
		$info = json_decode($this->request->getContent(), true);
		foreach ($info['downloads'] as $notify) {
			/** @var ProjectModel $project */
			$project = $this->repo->get(ProjectModel::class, array('name' => $notify['name']));
			if ($project !== null) {
				$this->projectDirectory->notifyDownload($project, $notify['version']);
			}
		}

		$this->finish(new Response(status: 201));
	}
}
