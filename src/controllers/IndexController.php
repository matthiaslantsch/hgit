<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\holofw\attributes\auth\Authorised;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends \holonet\holofw\Controller {
	#[Route('/', name: 'homepage')]
	public function index(): void {
		$this->shareGlobal('title', 'Project Index');

		$this->render('index');
	}

	#[Route('/login', name: 'login')]
	#[Authorised(permission: 'HGIT_USE')]
	public function login(): void {
		$this->redirectInternal('homepage');
	}

	#[Route('/logout', name: 'logout')]
	#[Authorised(permission: 'HGIT_USE')]
	public function logout(): void {
		if ($this->hasSession()) {
			$this->destroySession();
		}
		$this->redirectInternal('homepage');
	}
}
