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
use holonet\hgit\helpers\AccessMask;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\enum\ProjectType;
use holonet\hgit\helpers\ProjectDirectoryService;

/**
 * ProjectsController exposing a crud interface for the project resource.
 */
class ProjectsController extends HgitControllerBase {
	/**
	 * @var ProjectDirectoryService $di_directoryService Project directory filesystem service
	 */
	public $di_directoryService;

	/**
	 * POST projects.
	 */
	public function create(): void {
		if (!$this->authoriseUser()) {
			return;
		}

		/** @var User $sessionuser */
		$sessionuser = $this->session()->get('user');

		$data = array(
			'major' => 0, 'minor' => 0, 'fix' => 0,
			'name' => strip_tags($this->request->request->get('name')),
			'description' => strip_tags($this->request->request->get('description')),
			'permissionPreset' => $this->request->request->get('permPreset'),
			'type' => new ProjectType($this->request->request->get('projectType'))
		);

		if ($sessionuser->internalid !== null) {
			$data['user'] = $this->di_repo->find(UserModel::class, $sessionuser->internalid);
		}

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
			$this->view->set('redirect', $this->linkInternal($project->slugname()));
			$this->di_database->commit();
		} else {
			$this->view->set('errors', $errors->getAll());
		}

		$this->respondTo('json');
	}

	/**
	 * GET /projects
	 * ANY /.
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

	/**
	 * GET projects/new.
	 */
	public function new(): void {
		if (!$this->authoriseUser()) {
			return;
		}
		$this->view->set('projectTypes', ProjectType::toArray());
		$this->view->set('title', 'Create a new project');
	}

	/**
	 * GET /[projectName:a]
	 * show an overview of a project.
	 * @param string $projectName Alphanumeric string containing the project name
	 */
	public function show(string $projectName): void {
		/** @var ProjectModel|null $project */
		$project = $this->di_repo->get(ProjectModel::class, array('name' => $projectName));
		if ($project === null) {
			$this->notFound("project with the name '{$projectName}'");

			return;
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
