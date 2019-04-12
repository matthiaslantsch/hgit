<?php
# This file should contain all the record creation needed to seed the database with its default values.
# The data can then be loaded with the db/schema::seed or with the complete db/schema::setup task
#
# Examples:
#
#   $stones = models\StoneModel::create(array("name" => "a stone"));

use holonet\hgit\models as models;

models\ProjectTypeModel::create(array("name" => "php library"), true);
models\ProjectTypeModel::create(array("name" => "php app"), true);
models\ProjectTypeModel::create(array("name" => "holofw app"), true);
models\ProjectTypeModel::create(array("name" => "other"), true);

//include this as soon as we need it
return;
models\ActivityTypeModel::create(array("name" => "push"), true);
models\ActivityTypeModel::create(array("name" => "merge"), true);
models\ActivityTypeModel::create(array("name" => "release"), true);
models\ActivityTypeModel::create(array("name" => "wiki"), true);
models\ActivityTypeModel::create(array("name" => "misc"), true);

models\IssueTypeModel::create(array("name" => "bug"), true);
models\IssueTypeModel::create(array("name" => "hotfix"), true);
models\IssueTypeModel::create(array("name" => "feature"), true);

models\IssueStatusModel::create(array("name" => "new"), true);
models\IssueStatusModel::create(array("name" => "open"), true);
models\IssueStatusModel::create(array("name" => "resolved"), true);
models\IssueStatusModel::create(array("name" => "reopened"), true);
