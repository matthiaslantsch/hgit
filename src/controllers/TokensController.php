<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use DateTime;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use holonet\activerecord\Model;
use holonet\hgit\auth\PermissionMask;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\TokenModel;
use holonet\holofw\attributes\auth\IsResourceOwner;
use holonet\holofw\attributes\RequestBody;
use holonet\holofw\base\rest\Deletable;
use holonet\holofw\base\rest\RESTController;

#[IsResourceOwner]
class TokensController extends RESTController {
	use Deletable;

	public const int TOKEN_LENGTH = 40;

	public function create(
		#[RequestBody('token.name')] string $name,
		#[RequestBody('token.expiry')] string $expiry,
	): void {
		$expiry = strtotime($expiry);
		if ($expiry < time()) {
			$expiry = strtotime('+1 week');
		}

		$token = $this->createToken();
		$permissions = new PermissionMask(0);
		$permissions->add(Permission::READCODE);

		$this->repo->create(TokenModel::class, array(
			'expiry' => new DateTime("@{$expiry}"),
			'user' => $this->getAppUser(),
			'name' => htmlspecialchars($name),
			'hash' => password_hash($token, \PASSWORD_DEFAULT),
			'permissions' => $permissions
		));

		$this->share('token', $token);
		$this->shareGlobal('title', 'Token created successfully');
		$this->render('user/new-token');
	}

	public function index(): void {
		$this->restIndex();

		$this->render('user/tokens');
	}

	public function modelDeleted(Model $deleted): never {
		$this->flash('success', 'Token revoked successfully');

		parent::modelDeleted($deleted);
	}

	protected function createToken(): string {
		$generator = new ComputerPasswordGenerator();
		$generator->setLength(static::TOKEN_LENGTH);
		$generator->setSymbols();

		return $generator->generatePassword();
	}
}
