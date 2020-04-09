<?php

use holonet\dbconnect\Connector;
use holonet\dbconnect\PDOMysqlConnector;

require_once "vendor/autoload.php";

$oldDbName = "olddatabase";
$newDbName = "newdatase";

$connection = array(
	"host" => "localhost",
	"username" => "root",
	"password" => "password"
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

	$project['name'] = str_replace('-', '/', $project);

	$insert = sprintf('INSERT INTO project VALUES (%s)', implode(', ', array_fill(0, count($project), '?')));
	$count += $newDb->queryChange($insert, $project);
}

echo "Migrated {$count} projects\n";

$newDb->commit();


