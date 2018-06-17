<?php
/**
* This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * class file for the HgitAuthHandler class
 *
 * @package holonet framework
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@gmail.com>
 */

namespace holonet\hgit\helpers;

use holonet\holofw\auth\AuthHandler;
use holonet\hgit\models\UserModel;
use holonet\hgit\models\ProjectModel;

/**
 * HgitAuthHandler class used to authenticate a user
 * via the database and authorise him as well
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\helpers
 */
class HgitAuthHandler extends AuthHandler {

	/**
	 * authenticate a user using a http digest header
	 *
	 * @access public
	 * @param  array $digest The parsed digest to work with
	 * @return true or false on success or not
	 */
	public function digestAuth(array $digest) {
		$user = UserModel::get(array("name" => $digest["username"], "authdigest[!]" => null));

		if($user === null) {
			return false;
		}
		// generate the valid response
		$validResponse = md5(
			"{$user->authdigest}:{$digest['nonce']}:{$digest['nc']}:{$digest['cnonce']}:{$digest['qop']}:{$digest["a2checksum"]}"
		);

		if ($digest['response'] != $validResponse) {
			return false;
		} else {
			$this->session->user = new HgitUser($user);
			return true;
		}
	}

	/**
	 * authenticate a user based on a password and a username
	 *
	 * @access public
	 * @param  string $username The username to be authenticated
	 * @param  string $password The password to be used
	 * @return true or false on success or not
	 */
	public function passwordAuth(string $username, string $password) {
		$user = UserModel::get(array("name" => $username, "authhash[!]" => null));

		if($user === null) {
			return false;
		}

		if(password_verify($password, $user->authhash)) {
			$this->session->user = new HgitUser($user);
			return true;
		} else {
			return false;
		}
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
	 * @param  HgitUser $user Optional paramter for submitting a user object
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
