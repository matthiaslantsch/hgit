<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

require_once dirname(__DIR__)."/vendor/autoload.php";

if (php_sapi_name() == "cli-server") {
	// running under built-in server so
	// route static assets and return false
	$extensions = array("jpg", "jpeg", "gif", "css", "js", "ico");
	$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	$ext = pathinfo($path, PATHINFO_EXTENSION);
	if (in_array($ext, $extensions)) {
		return false;
	}
}

$app = new holonet\hgit\Application();
$request = \holonet\http\Request::createFromGlobals();
$resp = $app->handleRequest($request);
$resp->send();

