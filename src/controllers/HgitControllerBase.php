<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\holofw\FWController;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\HgitAuthoriser;
use holonet\hgit\models\enum\ProjectType;

/**
 * abstract HgitControllerBase base class for every hgit controller
 * contains commonly used logic throughout all controllers.
 */
abstract class HgitControllerBase extends FWController {
	/**
	 * facade method sorting the parameter data
	 * "unslugs" the project name if one was submitted.
	 */
	public function __before(): void {
		if ($this->request->attributes->has('projectName')) {
			$this->request->attributes->set('projectName',
				str_replace('-', '/', $this->request->attributes->get('projectName'))
			);
		}
	}

	/**
	 * small helper method checking the authorisation on a project
	 * calls the not allowed method to throw an exception if the access is denied.
	 * @param ProjectModel $project The project that needs access to a function checked
	 * @param string $function Function string describing the function the user wants to access in that project
	 * @return bool true on allowed or false
	 */
	protected function accessControl(ProjectModel $project, string $function): bool {
		if (!HgitAuthoriser::checkAuthorisation($project, $function)) {
			//anonymous access failed, auth the user
			//check if the user is allowed to access hgit as a "user" first
			if (!$this->authoriseUser()) {
				return false;
			}
			if (!HgitAuthoriser::checkAuthorisation($project, $function, $this->session()->get('user'))) {
				throw $this->notAllowed("Function '{$function}' for project '{$project->name}' was denied");
			}
		}

		return true;
	}

	/**
	 * small helper method collecting an array of projects that are visible
	 * to the current user (logged in or anonymous).
	 * @param ProjectType|null $typeFilter Filter for specific project types
	 * @return ProjectModel[] with projects that should be visible in the current context
	 * @psalm-suppress LessSpecificReturnStatement
	 * @psalm-suppress MoreSpecificReturnType
	 */
	protected function getVisibleProjects(?ProjectType $typeFilter = null): array {
		if (!isset($this->session) || !$this->session->has('user')) {
			$options = array('anyMask[!]' => 0);
		} else {
			$hgituser = $this->session->get('user');
			if (empty($hgituser->permissions['projects'])) {
				return array();
			}
			$options = array('idProject' => array_keys($hgituser->permissions['projects']));
		}

		if ($typeFilter !== null) {
			$options['type'] = $typeFilter;
		}

		return $this->di_repo->select(ProjectModel::class, $options);
	}
}
