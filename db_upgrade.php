<?php

use holonet\dbconnect\Connector;
use holonet\dbconnect\PDOMysqlConnector;

require_once "vendor/autoload.php";

$oldDbName = "hgit_prod";
$newDbName = "hgit";

$connection = array(
	"host" => "localhost",
	"username" => "root",
	"socket" => "/run/mysqld/mysqld10.sock",
	"password" => "H0l0sql2018"
);

$oldDb = new PDOMysqlConnector($connection);
$oldDb->exec("USE {$oldDbName}");
$newDb = new PDOMysqlConnector($connection);
$newDb->exec("USE {$newDbName}");

$newDb->transaction();

echo "Migrating projects...\n";

$oldData = $oldDb->queryAll('SELECT project.*, projectType.name AS type FROM project JOIN projectType USING(idProjectType)');
$count = 0;

foreach ($oldData as $project) {
	unset($project['idProjectType']);
	unset($project['idProject']);

	$project['name'] = str_replace('-', '/', $project['name']);
	$project['description'] = $project['description'] ?? '';
	$project['idUser'] = 1;

	$insert = sprintf('INSERT INTO project (`%s`) VALUES (%s)', implode('`, `', array_keys($project)), implode(', ', array_fill(0, count($project), '?')));
	$count += $newDb->queryChange($insert, array_values($project));
}

echo "Migrated {$count} projects\n";

$newDb->commit();
