<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\hgit\models\ProjectModel;
use holonet\hgit\utils\VisibleProjectsOptionsTrait;
use holonet\holofw\attributes\auth\Authorised;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends \holonet\holofw\Controller {
	use VisibleProjectsOptionsTrait;

	#[Route('/', name: 'homepage')]
	public function index(): void {
		$this->shareGlobal('title', 'Project Index');

		$this->share('projects', $this->repo->select(ProjectModel::class, $this->projectIndexOptions()));

		$this->render('index');
	}

	#[Authorised(permission: 'HGIT_USE')]
	#[Route('/login', name: 'login')]
	public function login(): void {
		$this->redirectInternal('homepage');
	}

	#[Route('/logout', name: 'logout')]
	public function logout(): void {
		if ($this->hasSession()) {
			$this->destroySession();
		}
		$this->redirectInternal('homepage');
	}
}
