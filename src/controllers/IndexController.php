<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use Symfony\Component\Routing\Annotation\Route;

/**
 * IndexController class for the index controller
 * handles common stuff like login and logout.
 */
class IndexController extends HgitControllerBase {
	/**
	 * @Route("/login", name="login")
	 */
	public function login(): void {
		if ($this->authoriseUser() === false) {
			return;
		}
		$this->redirectInternal('homepage');
	}

	/**
	 * @Route("/logout", name="logout")
	 */
	public function logout(): void {
		if ($this->session !== null) {
			$this->session->clear();
			$this->session->save();
			$this->session->invalidate();
			$this->session->save();
		}
		$this->redirectInternal('homepage');
	}
}
