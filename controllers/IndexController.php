<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the IndexController
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

/**
 * IndexController class for the index controller
 * handles common stuff like login and logout
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\controllers
 */
class IndexController extends HgitControllerBase {

	/**
	 * ANY /logout
	 * small method logging the user out and redirecting him to the dashboard
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function logout() {
		$this->session()->destroy();
		$this->redirectInternal();
	}

	/**
	 * ANY /login
	 * small method logging the user in and redirecting him back to the dashboard
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function login() {
		$this->authenticateUser();
		$this->redirectInternal();
	}

}
