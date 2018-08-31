<?php
/**
* This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * class file for the HgitUser class
 *
 * @package holonet framework
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@gmail.com>
 */

namespace holonet\hgit\helpers;

use holonet\session\User;
use holonet\hgit\models\UserModel;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\GroupAccessModel;

/**
 * HgitUser used to hold the information about a user
 * persistent across requests
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\helpers
 */
class HgitUser extends User {

	/**
	 * constructor method used to create a session user object
	 * using a UserModel instance
	 *
	 * @access public
	 * @param  UserModel $user The user that was logged in
	 * @return void
	 */
	public function __construct(UserModel $user) {
		$this->username = $user->name;
		$this->name = $user->name;
		$this->email = $user->email;
		$this->db_id = $user->id;

		foreach ($user->groups as $group) {
			$this->groups[$group->id] = $group->name;
		}

		//allow the user access to all projects that allow anything to the public
		foreach (ProjectModel::select(array("anyMask[!]" => 0)) as $publicProject) {
			$this->permissions[$publicProject->idProject] = $publicProject->anyMask;
		}

		//allow the user access to all projects that allow anything to "internal" users
		foreach (ProjectModel::select(array("otherMask[!]" => 0)) as $internalProject) {
			$this->permissions[$internalProject->idProject] = $internalProject->otherMask;
		}

		//@TODO this will probably not work as intended, just don't use groups atm
		if(!empty($this->groups)) {
			foreach (GroupAccessModel::select(array("group.name" => $this->groups)) as $accessMask) {
				if(isset($this->permissions[$accessMask->idProject])) {
					$this->permissions[$accessMask->idProject] |= $accessMask->mask;
				} else {
					$this->permissions[$accessMask->idProject] = $accessMask->mask;
				}
			}
		}

		//allow the user to access all his projects (the projects where he is maintainer)
		foreach ($user->projects as $maintainedProject) {
			$this->permissions[$maintainedProject->id] = new AccessMask(255);
		}
	}

}
