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

##
## user #
##
$schema->createTable("user", function(TableBuilder $table) {
	$table->string("nick");
	$table->addColumn("externalid", "uuid");
	$table->string("email")->nullable();
	$table->version("1512584325");
});

##
## project #
##
$schema->createTable("project", function(TableBuilder $table) {
	$table->string("name", 20)->unique();
	$table->text("description")->nullable();
	$table->string("major", 10);
	$table->string("minor", 10);
	$table->string("fix", 10);
	$table->integer("otherMask");
	$table->integer("anyMask")->default(0);
	$table->integer("idUser");
	$table->string("type", 10);
	$table->version("1512584326");
});

##
## activity #
##
$schema->createTable("activity", function(TableBuilder $table) {
	$table->text("content");
	$table->integer("idUser")->nullable();
	$table->integer("idProject");
	$table->string("type", 10);
	$table->timestamps();
	$table->version("1512584328");
});

##
## issue #
##
$schema->createTable("issue", function(TableBuilder $table) {
	$table->string("title", 40);
	$table->text("description");
	$table->string("targetVersion", 15)->nullable();
	$table->integer("idProject");
	$table->integer("author");
	$table->string("type", 10);
	$table->string("status", 10);
	$table->timestamps();
	$table->version("1512584331");
});

##
## comment #
##
$schema->createTable("comment", function(TableBuilder $table) {
	$table->text("description");
	$table->integer("idIssue");
	$table->integer("idUser");
	$table->timestamps();
	$table->version("1512584332");
});

##
## group #
##
$schema->createTable("group", function(TableBuilder $table) {
	$table->string("name", 60);
	$table->version("1512584333");
});

##
## userAccess #
##
$schema->createTable("userAccess", function(TableBuilder $table) {
	$table->integer("mask");
	$table->integer("idProject");
	$table->integer("idUser");
	$table->version("1512584335");
});

##
## groupAccess #
##
$schema->createTable("groupAccess", function(TableBuilder $table) {
	$table->integer("mask");
	$table->integer("idProject");
	$table->integer("idGroup");
	$table->version("1512584336");
});

##
## group2user #
##
$schema->createResolutionTable("group", "user", "1512584334");

##
## project references #
##
$schema->changeTable("project", function(TableBuilder $table) {
	$table->addReference("user", "idUser", "idUser");
	$table->version("1512584326");
});

##
## activity references #
##
$schema->changeTable("activity", function(TableBuilder $table) {
	$table->addReference("user", "idUser", "idUser");
	$table->addReference("project", "idProject", "idProject");
	$table->version("1512584328");
});

##
## issue references #
##
$schema->changeTable("issue", function(TableBuilder $table) {
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("user", "author", "idUser");
	$table->version("1512584331");
});

##
## comment references #
##
$schema->changeTable("comment", function(TableBuilder $table) {
	$table->addReference("issue", "idIssue", "idIssue");
	$table->addReference("user", "idUser", "idUser");
	$table->version("1512584332");
});

##
## userAccess references #
##
$schema->changeTable("userAccess", function(TableBuilder $table) {
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("user", "idUser", "idUser");
	$table->version("1512584335");
});

##
## groupAccess references #
##
$schema->changeTable("groupAccess", function(TableBuilder $table) {
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("group", "idGroup", "idGroup");
	$table->version("1512584336");
});