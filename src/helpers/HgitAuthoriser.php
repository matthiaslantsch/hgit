<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use InvalidArgumentException;
use holonet\holofw\session\User;
use holonet\holofw\auth\AuthFlow;
use holonet\hgit\models\UserModel;
use holonet\holofw\auth\Authoriser;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\GroupAccessModel;
use holonet\http\error\NotAllowedException;

/**
 * HgitAuthoriser class used to authorise a user using local permission masks.
 */
class HgitAuthoriser implements Authoriser {
	public const HGIT_USER_PERMISSION = 'hgit_use';

	/**
	 * @var AuthFlow $flow Reference to the authentication flow that uses this class
	 */
	public $flow;

	/**
	 * @param AuthFlow $flow Reference to the authentication flow using this authenticator
	 */
	public function __construct(AuthFlow $flow) {
		$this->flow = $flow;
	}

	/**
	 * {@inheritdoc}
	 * i.e. load permission masks for projects and determine which projects this user is allowed to access.
	 */
	public function authorise(User $user): void {
		if (!$user->hasPermission(static::HGIT_USER_PERMISSION)) {
			throw new NotAllowedException("Authenticated user with ext id {$user->externalid} is not allowed to access the hgit app");
		}

		$dbUser = $this->flow->tryImportUser($user);
		if (!$dbUser instanceof UserModel) {
			throw new InvalidArgumentException('Db user model given to HgitAuthoriser must be of type '.UserModel::class);
		}

		$permissions = array();

		//allow the user access to all projects that allow anything to the public
		foreach ($this->flow->di_repo->selectColumn(ProjectModel::class, 'anyMask', array('anyMask[!]' => 0)) as $projId => $anyMask) {
			$permissions[$projId] = new AccessMask($anyMask);
		}

		//allow the user access to all projects that allow anything to "internal" users
		foreach ($this->flow->di_repo->selectColumn(ProjectModel::class, 'otherMask', array('otherMask[!]' => 0)) as $projId => $otherMask) {
			$permissions[$projId] = new AccessMask($otherMask);
		}

		if (count($dbUser->groups) > 0) {
			$groupAccesses = $this->flow->di_repo->select(GroupAccessModel::class, array('
				group.idGroup' => array_column($dbUser->groups->toArray(), 'idGroup')
			));
			foreach ($groupAccesses as $accessMask) {
				if (isset($permissions[$accessMask->idProject])) {
					$permissions[$accessMask->idProject] |= $accessMask->mask;
				} else {
					$permissions[$accessMask->idProject] = $accessMask->mask;
				}
			}
		}

		//allow the user to access all his projects (the projects where he is maintainer)
		foreach ($dbUser->projects as $maintainedProject) {
			$permissions[$maintainedProject->id] = new AccessMask(255);
		}

		$user->permissions['projects'] = $permissions;
	}

	/**
	 * check if the user is allowed to access a certain function on a given project
	 * makes use of the integer bitmask in the database
	 * will check in the following order to reduce lookups:
	 *  check if the function is globally allowed (public perm mask)
	 *  check if internally allowed (other perm mask)
	 *  check if the user has his own permission mask.
	 * @param ProjectModel $project The project to be checked if the user has access or not
	 * @param string $function The function the user should have access to to get a return of true
	 * @param User $user Optional parameter for submitting a hgit session user object
	 * @return bool true or false on allowed or not
	 */
	public static function checkAuthorisation(ProjectModel $project, string $function = 'see', User $user = null): bool {
		//check if the public permission int of this project already allows this
		if ($project->anyMask->doesAllow($function)) {
			return true;
		}

		//check if the user is logged in
		if (!isset($user)) {
			return false;
		}

		//check if the internal permission int of this project allows this
		if ($project->otherMask->doesAllow($function)) {
			return true;
		}

		if (isset($user->permissions['projects'][$project->id])) {
			return $user->permissions['projects'][$project->id]->doesAllow($function);
		}

		return false;
	}
}
