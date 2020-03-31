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
	"username" => "%env(DB_USER)%",
	"password" => "%env(DB_PASSWORD)%",
);

$config["vendorInfo"] = array(
	"namespace" => "holonet"
);

$config["auth"] = array(
	"realm" => "sphinx_auth",
	"flow" => \holonet\sphinxauth\SphinxAuthFlow::class,
	"handler" => \holonet\sphinxauth\SphinxAuthenticator::class,
	"authoriser" => \holonet\hgit\helpers\HgitAuthoriser::class,
	"usermodel" => \holonet\hgit\models\UserModel::class,
	"sphinx" => array(
		"provider_url" => "%env(SPHINX_URL)%",
		"client_id" => "%env(SPHINX_CLIENT_ID)%",
		"client_secret" => "%env(SPHINX_CLIENT_SECRET)%",
		"realm" => "%env(SPHINX_REALM)%"
	)
);

$config["gitExe"] = "C:\\Program Files\\Git\\bin\\git";
