<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use RuntimeException;
use holonet\holofw\session\User;
use holonet\hgit\models\UserModel;
use holonet\holofw\annotation\REST;
use holonet\hgit\helpers\AccessMask;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\enum\ProjectType;
use holonet\hgit\views\helpers\ViewUtils;
use Symfony\Component\Routing\Annotation\Route;
use holonet\hgit\services\ProjectDirectoryService;

/**
 * ProjectsController exposing a crud interface for the project resource.
 * @REST("project")
 */
class ProjectsController extends HgitControllerBase {
	public ProjectDirectoryService $di_directoryService;

	public function create(): void {
		if (!$this->authoriseUser()) {
			return;
		}

		/** @var User $sessionuser */
		$sessionuser = $this->session()->get('user');

		$data = array(
			'name' => strip_tags($this->request->request->get('name')),
			'description' => strip_tags($this->request->request->get('description')),
			'permissionPreset' => $this->request->request->get('permPreset'),
			'type' => ProjectType::fromValue($this->request->request->get('projectType'))
		);

		$data['user'] = $this->di_repo->find(UserModel::class, $sessionuser->internalid);

		/** @var ProjectModel $project */
		$project = $this->di_repo->new(ProjectModel::class, $data);

		$errors = $project->valid();
		if ($errors === true) {
			$this->di_database->transaction();
			if (!$project->save()) {
				throw new RuntimeException('Could not save new project valid?:'.var_export($project->valid(), true));
			}

			$this->di_directoryService->create($project);

			//save the new permission in the current session
			$sessionuser->permissions['projects'][$project->id] = new AccessMask(255);

			$this->view->set('errors', false);
			$this->view->set('redirect', ViewUtils::linkTo('projects_show', array('projectName' => $project->slugname())));
			$this->di_database->commit();
		} else {
			$this->view->set('errors', $errors->getAll());
		}

		$this->respondTo('json');
	}

	/**
	 * @Route("/", methods={"GET"}, name="homepage")
	 */
	public function index(): void {
		$this->view->set('title', 'Project Overview');
		$projects = $this->getVisibleProjects();
		$statistics = array();
		foreach ($projects as $proj) {
			$projectDir = $this->di_directoryService->projectDirectory($proj);
			$statistics[$proj->id] = $this->di_directoryService->getStatistics($projectDir);
		}
		$this->view->set('statistics', $statistics);
		$this->view->set('projects', $projects);
	}

	public function new(): void {
		if (!$this->authoriseUser()) {
			return;
		}
		$this->view->set('projectTypes', ProjectType::toArray());
		$this->view->set('title', 'Create a new project');
	}

	/**
	 * @Route("/{projectName}", methods={"get"})
	 */
	public function show(string $projectName): void {
		/** @var ProjectModel|null $project */
		$project = $this->di_repo->get(ProjectModel::class, array('name' => $projectName));
		if ($project === null) {
			throw $this->notFound("project with the name '{$projectName}'");
		}

		$projectDir = $this->di_directoryService->projectDirectory($project);

		if (!$this->accessControl($project, 'see')) {
			return;
		}

		$this->accessControl($project, 'see');
		$this->view->set('title', "{$project->name} Overview");
		$this->view->set('project', $project);
		$this->view->set('statistics', $this->di_directoryService->getStatistics($projectDir));
	}
}
