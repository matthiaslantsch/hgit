<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the abstract HgitControllerBase base class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\holofw\FWController;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\HgitAuthoriser;

/**
 * abstract HgitControllerBase base class for every hgit controller
 * contains commonly used logic throughout all controllers
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\controllers
 */
abstract class HgitControllerBase extends FWController {

	/**
	 * small helper method checking the authorisation on a project
	 * calls the not allowed method to throw an exception if the access is denied
	 *
	 * @access protected
	 * @param  ProjectModel $project The project that needs access to a function checked
     * @param  string $function Function string decribing the function the user wants to access in that project
	 * @return void
	 */
	protected function accessControl(ProjectModel $project, string $function) {
		if(!HgitAuthoriser::checkAuthorisation($project, $function)) {
			//anonymous access failed, auth the user
			//check if the user is allowed to access hgit as a "user" first
			$this->checkAccess();
			if(!HgitAuthoriser::checkAuthorisation($project, $function, $this->session->hgituser)) {
				$this->notAllowed("Function '{$function}' for project '{$project->name}' was denied");
			}
		}
	}

	/**
	 * small helper method collecting an array of projects that are visible
	 * to the current user (logged in or anonymous)
	 *
	 * @access protected
	 * @return array with projects that should be visible in the current context
	 */
	protected function getVisibleProjects() {
		if($this->session === null || !$this->session->has("hgituser")) {
			return ProjectModel::select(array("anyMask[!]" => 0));
		} else {
			return ProjectModel::select(array(
				"idProject" => array_keys($this->session->hgituser->permissions)
			));
		}
	}

}
