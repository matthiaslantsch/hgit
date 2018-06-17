<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the ComposerController
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use RuntimeException;
use holonet\hgit\models\ComposerPackage;
use holonet\hgit\models\UserModel;
use holonet\http\HttpResponse;

/**
 * ComposerController exposing that is supposed to communicate with
 * a composer client and advertise the php library projects
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\controllers
 */
class ComposerController extends HgitControllerBase {

	/**
	 * GET /composer/packages.json
	 * return a json list with information about this composer backend
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function info() {
		$this->authenticateUser();

		$packages = array();
		foreach (ComposerPackage::all() as $phplib) {
			$packages[$phplib->name] = $phplib->versions();
		}
		yield "packages" => $packages;
		yield "notify" => "/composer/notify/%package%";
		yield "notify-batch" => "/composer/notify/";
		//yield "search" => "/composer/search.json?q=%query%&type=%type%";

		$this->respondTo("json");
	}

	/**
	 * POST /composer/notify/[projectName:?s]
	 * backend for collecting installation data from the composer clients
	 *
	 * @access public
	 * @param  string $packageName Optional parameter with the package name that was installed
	 * @return the yield from the controller method
	 */
	public function notify($packageName) {
		$info = json_decode($this->request->body(), true);
		foreach ($info["downloads"] as $notify) {
			if(($project = ProjectModel::get(array("name" => $notify["name"]))) !== null) {
				$project->notifyDownload($notify["version"]);
			} else {
				$this->notFound("Could not find installed package '{$notify["name"]}'");
			}
		}
	}

	/**
	 * GET /composer/search.json?q=%query%&type=%type%
	 * a search interface for the composer client
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function search() {
		$this->authenticateUser();

		//@TODO implement the composer type parameter
		$query = strip_tags($this->request->query->get("query"));
		$options = array(
			"projectType" => ProjectTypeModel::get(array("name" => "php library")),
			"name[~]" => $query
		);
		$packages = ProjectModel::select($options);
		die(var_dump($packages));
	}

}
