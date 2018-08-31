<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the ProjectsController
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use RuntimeException;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\UserModel;
use holonet\hgit\models\ProjectTypeModel;
use holonet\hgit\helpers\AccessMask;

/**
 * ProjectsController exposing a crud interface for the project resource
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\controllers
 */
class ProjectsController extends HgitControllerBase {

	/**
	 * GET /projects
	 * ANY /
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function index() {
		yield "title" => "Project Overview";
		yield "projects" => $this->getVisibleProjects();
	}

	/**
	 * GET projects/new
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function new() {
		$this->authoriseUser();
		yield "projectTypes" => ProjectTypeModel::all();
		yield "title" => "Create a new project";
	}

	/**
	 * POST projects
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function create() {
		$this->authoriseUser();
		$rawdata = $this->request->request->getAll(array(
			"name", "description", "permPreset", "projectType"
		));

		$data = array(
			"major" => 0, "minor" => 0, "fix" => 0,
			"name" => strip_tags($rawdata["name"]),
			"description" => strip_tags($rawdata["description"]),
			"permissionPreset" => $rawdata["permPreset"],
			"user" => UserModel::find($this->session->hgituser->db_id),
			"idProjectType" => intval($rawdata["projectType"])
		);

		$project = new ProjectModel($data);

		$errors = $project->valid();
		if($errors === true) {
			if(!$project->save()) {
				throw new RuntimeException("Could not save new project valid?:".var_export($project->valid(), true));
			}

			//save the new permission in the current session
			$this->session->hgituser->permissions[$project->id] = new AccessMask(255);

			yield "errors" => false;
			yield "redirect" => $this->linkInternal($project->slugname());
		} else {
			yield "errors" => $errors->getAll();
		}

		$this->respondTo("json");
	}

	/**
	 * GET /[projectName:a]
	 * show an overview of a project
	 *
	 * @access public
	 * @param  string $projectName Alphanumeric string containing the project name
	 * @return the yield from the controller method
	 */
	public function show(string $projectName) {
		$project = ProjectModel::get(array("name" => $projectName));
		if($project === null) {
			$this->notFound("project with the name '{$projectName}'");
		}

		$this->accessControl($project, "see");
		yield "title" => "{$project->name} Overview";
		yield "project" => $project;
	}

	/**
	 * facade method sorting the parameter data
	 * "unslugs" the project name if one was submitted
	 *
	 * @access public
	 * @return void
	 */
	public function __before() {
		if($this->request->params->has("projectName")) {
			$this->request->params->set("projectName",
				str_replace("-", "/", $this->request->params->get("projectName"))
			);
		}
	}

}
