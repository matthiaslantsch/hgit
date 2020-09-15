<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch
 *
 * @package hgit app
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

$config["timezone"] = "Europe/Zurich";

$config["database"] = array(
	"driver" => "%env(DB_DRIVER)%",
	"file" => "%env(DB_FILE)%",
	"host" => "%env(DB_HOST)%",
	"port" => "%env(DB_PORT)%",
	"socket" => "%env(DB_SOCKET)%",
	"username" => "%env(DB_USER)%",
	"password" => "%env(DB_PASSWORD)%",
);

if(filter_var($_ENV['USE_REMOTE_AUTH_SYSTEM'], FILTER_VALIDATE_BOOLEAN)) {
	$config["auth"] = array(
		"realm" => "sphinx_auth",
		"flow" => \holonet\sphinxauth\SphinxAuthFlow::class,
		"handler" => \holonet\sphinxauth\SphinxAuthenticator::class,
		"sphinx" => array(
			"provider_url" => "%env(SPHINX_URL)%",
			"client_id" => "%env(SPHINX_CLIENT_ID)%",
			"client_secret" => "%env(SPHINX_CLIENT_SECRET)%",
			"realm" => "%env(SPHINX_REALM)%"
		)
	);
} elseif(filter_var($_ENV['DEV_MODE'], FILTER_VALIDATE_BOOLEAN)) {
	$config["auth"] = array(
		"flow" => \holonet\holofw\auth\flow\PromptAuthFlow::class,
		"handler" => \holonet\holofw\auth\handler\DevAuthHandler::class
	);
} else {
	$config["auth"] = array(
		"flow" => \holonet\holofw\auth\flow\PromptAuthFlow::class,
		"handler" => \holonet\holofw\auth\handler\FlatfileAuthHandler::class
	);
}

$config["auth"]["authoriser"] = \holonet\hgit\helpers\HgitAuthoriser::class;
$config["auth"]["usermodel"] = \holonet\hgit\models\UserModel::class;

$config["gitExe"] = "%env(GIT_BIN_PATH)%";

$config["vendorInfo"] = array(
	"namespace" => "holonet",
	"author" => array(
		"name" => "Matthias Lantsch",
		"email" => "matthias.lantsch@bluewin.ch"
	),
	"license" => "http://opensource.org/licenses/gpl-license.php  GNU Public License",
	"partOf" => "the holonet project management software"
);
