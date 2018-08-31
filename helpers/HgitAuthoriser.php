<?php
/**
* This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * class file for the HgitAuthoriser class
 *
 * @package holonet framework
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@gmail.com>
 */

namespace holonet\hgit\helpers;

use holonet\holofw\auth\Authoriser;
use holonet\hgit\models\UserModel;
use holonet\hgit\models\ProjectModel;
use holonet\session\Session;

/**
 * HgitAuthoriser class used to authorise a user
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\helpers
 */
class HgitAuthoriser extends Authoriser {

	/**
	 * authorise a user (meaning save permission information to an already existing user object)
	 *
	 * @access public
	 * @param  Session $session A session object from the session library
	 * @return void
	 */
	public static function authorise(Session $session) {
		if(($hgituser = UserModel::get(array("nick" => $session->user->username))) === null) {
			$hgituser = UserModel::create(array(
				"nick" => $session->user->username,
				"name" => $session->user->name,
				"email" => $session->user->email
			), true);
		}
		$session->hgituser = new HgitUser($hgituser);
	}

	/**
	 * check if the user is allowed to access a certain function on a given project
	 * makes use of the integer bitmask in the database
	 * will check in the following order to reduce lookups:
	 *  check if the function is globally allowed (public perm mask)
	 *  check if internally allowed (other perm mask)
	 *  check if the user has his own permission mask
	 *
	 * @access public
	 * @param  ProjectModel $project The project to be checked if the user has access or not
	 * @param  string $function The function the user should have access to to get a return of true
	 * @param  HgitUser $user Optional paramter for submitting a hgit session user object
	 * @return true or false on allowed or not
	 */
	public static function checkAuthorisation(ProjectModel $project, string $function = "see", HgitUser $user = null) {
		//check if the public permission int of this project already allows this
		if($project->anyMask->doesAllow($function)) {
			return true;
		}
		//check if the user is logged in
		if(!isset($user)) {
			return false;
		}

		//check if the internal permission int of this project allows this
		if($project->otherMask->doesAllow($function)) {
			return true;
		}

		if(isset($user->permissions[$project->id])) {
			return $user->permissions[$project->id]->doesAllow($function);
		} else {
			return false;
		}
	}

}
