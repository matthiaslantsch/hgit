<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\auth;

use holonet\activerecord\ModelRepository;
use holonet\common\di\autowire\attribute\ConfigItem;
use holonet\holofw\attributes\auth\Authorised;
use holonet\holofw\auth\AuthFlow;
use holonet\holofw\auth\Authoriser;
use holonet\holofw\auth\DefaultRoleCheckTrait;
use holonet\holofw\auth\handler\DevAuthHandler;
use holonet\holofw\auth\UserModel;
use holonet\holofw\Request;
use holonet\holofw\session\User;

class HgitAuthoriser implements Authoriser {
	use DefaultRoleCheckTrait;

	public function __construct(
		#[ConfigItem('auth_base_role')]
		public string $role,
		protected ModelRepository $repo
	) {}

	public function allowedForAction(AuthFlow $auth, Request $request, User $user, Authorised $authorised): void {
		$authorised->role ??= $this->role;

		// @todo hack
		if (str_starts_with($user->externalid, DevAuthHandler::class)) {
			$user->role = $this->role;
		}

		// check for global rule usage to ensure that roles set from the idp are used
		$this->defaultRoleAndPermissionCheck($auth, $request, $user, $authorised);
	}

	public function authorise(User $user): void {}

	public function imported(User $user, UserModel $model): void {}
}
