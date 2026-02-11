<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\auth;

use Attribute;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\ProjectModel;
use holonet\holofw\attributes\auth\Authorised;
use holonet\holofw\attributes\auth\Policy;
use holonet\holofw\auth\AuthFlow;
use holonet\holofw\Request;
use holonet\holofw\session\User;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ProjectAccess extends Policy {
	protected Authorised $authorised;

	public function __construct(
		public ?Permission $function = null,
		?string $role = null,
		?string $permission = null
	) {
		$this->authorised = new Authorised($role, $permission);
	}

	public function validateAccess(AuthFlow $auth, Request $request): ?User {
		$project = $request->attributes->get('model');
		if (!$project instanceof ProjectModel) {
			$this->forbidden($auth, $request, 'No project model found in request attributes');
		}

		// check if the public permission int of this project already allows this
		if ($project->publicPermissions->allows($this->function)) {
			return null;
		}

		// login + default role check
		$user = $this->authorised->authorise($auth, $request);

		// check if the internal permission int of this project allows this
		if ($project->internalPermissions->allows($this->function)) {
			return $user;
		}

		$permissions = $user->usermodel->effectivePermissions();
		if (isset($permissions[$project->id]) && $permissions[$project->id]->allows($this->function)) {
			return $user;
		}

		$functionName = $this->function?->name ?? 'see';
		$this->forbidden($auth, $request, "User '{$user->externalid}({$user->username})' is not allowed to access function '{$functionName}' on project '{$project->name}'");
	}
}
