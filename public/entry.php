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

$app = new holonet\hgit\Application();
$request = \holonet\holofw\Request::createFromGlobals();
$resp = $app->handleRequest($request);
$resp->send();

