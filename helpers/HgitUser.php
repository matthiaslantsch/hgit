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

		foreach ($user->projects as $maintainedProject) {
			$this->permissions[$maintainedProject->id] = new AccessMask(255);
		}

		foreach ($user->userAccesses as $accessMask) {
			$this->permissions[$accessMask->idProject] = $accessMask->mask;
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
	}

}
