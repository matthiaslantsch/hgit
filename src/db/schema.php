<?php
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.php definition is the main source for your
# database schema. To recreate the database, do not run all migrations, use the
# db/schema::load task
#
# It's strongly recommended that you check this file into your version control system.

use holonet\activerecord\Database;
use holonet\dbmigrate\builder\TableBuilder;

if(!isset($database) || !$database instanceof Database) {
	throw new LogicException("Cannot include database schema without supplying the database object");
}
$schema = $database->schema();

