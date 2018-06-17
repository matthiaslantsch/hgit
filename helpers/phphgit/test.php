<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * test script file
 */
require_once "/home/matthias/workspace/hdev/holoFW/init.php";
use HIS5\lib\Common as co;

co\registry("app.name", "hgit");
co\registry("app.path", "/home/matthias/workspace/hdev/hgit");

\HIS5\holoFW\core\Application::bootLoadProject();
restore_exception_handler();
restore_error_handler();
$repo = HIS5\hgit\helpers\phphgit\PHPHGit::access("/home/matthias/workspace/hdev/hgit");

$tree = $repo->branches["master"]->getHEAD()->getPath();
var_export($tree->getContent());