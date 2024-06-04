<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use holonet\holofw\Request;
use holonet\holofw\session\User;
use holonet\holofw\auth\AuthFlow;
use holonet\holofw\auth\UserModel;
use holonet\holofw\auth\Authoriser;
use holonet\holofw\attributes\auth\Authorised;
use holonet\holofw\auth\DefaultRoleCheckTrait;

class HgitAuthoriser implements Authoriser {
	use DefaultRoleCheckTrait;

	public function allowedForAction(AuthFlow $auth, Request $request, User $user, Authorised $authorised): void {
		// check for global rule usage to ensure that roles set from the idp are used
		$this->defaultRoleAndPermissionCheck($auth, $request, $user, $authorised);
		// @todo check for specific permission on the given project
	}

	public function authorise(User $user): void {
		// @todo collect the allowed projects for the user
	}

	public function imported(User $user, UserModel $model): void {}
}
