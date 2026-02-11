<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\activerecord\attributes\Table;
use holonet\activerecord\attributes\Unique;
use holonet\activerecord\relations\HasMany;
use holonet\activerecord\relations\Many2Many;
use holonet\common\verifier\rules\Required;
use holonet\common\verifier\rules\string\MaxLength;
use holonet\hgit\auth\PermissionMask;
use holonet\holofw\auth\StandardUserModelTrait;

#[Table('user')]
class UserModel extends \holonet\holofw\auth\UserModel {
	use StandardUserModelTrait;

	#[MaxLength(255)]
	public ?string $email = null;

	#[Unique]
	public ?string $externalId = null;

	public Many2Many $groups;

	public HasMany $projects;

	#[MaxLength(255)]
	#[Required]
	public string $username;

	public function effectivePermissions(): array {
		$permissions = array();

		// allow the user access to all projects that allow anything to the public
		foreach ($this->table()->repository->selectColumn(ProjectModel::class, 'publicPermissions', array('publicPermissions[!]' => 0)) as $projId => $publicPermissions) {
			$permissions[$projId] = new PermissionMask($publicPermissions);
		}

		// allow the user access to all projects that allow anything to "internal" users
		foreach ($this->table()->repository->selectColumn(ProjectModel::class, 'internalPermissions', array('internalPermissions[!]' => 0)) as $projId => $internalPermissions) {
			$permissions[$projId] = new PermissionMask($internalPermissions);
		}

		if (count($this->groups->get()) > 0) {
			$groupAccesses = $this->table()->repository->select(AccessModel::class, array('
				idGroup' => array_column($this->groups->arr(), 'id')
			));
			foreach ($groupAccesses as $accessMask) {
				if (isset($permissions[$accessMask->idProject])) {
					$permissions[$accessMask->idProject] |= $accessMask->mask;
				} else {
					$permissions[$accessMask->idProject] = $accessMask->mask;
				}
			}
		}

		// allow the user to access all his projects (the projects where he is maintainer)
		foreach ($this->projects->get() as $maintainedProject) {
			$permissions[$maintainedProject->id] = new PermissionMask(255);
		}

		return $permissions;
	}

	public static function supportedUserClaims(): array {
		return array(
			'username' => 'username',
			'email' => 'email'
		);
	}
}
