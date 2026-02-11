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
	$table->addColumn("externalId", "uuid")->nullable()->unique();
	$table->string("username");
	$table->string("email")->nullable();
	$table->version("1715901857");
});

##
## project #
##
$schema->createTable("project", function(TableBuilder $table) {
	$table->string("name", 40)->unique();
	$table->string("slug", 40)->unique();
	$table->text("description")->nullable();
	$table->integer("internalPermissions");
	$table->integer("publicPermissions")->default(0);
	$table->integer("idUser");
	$table->string("type", 12);
	$table->version("1715901858");
});

##
## group #
##
$schema->createTable("group", function(TableBuilder $table) {
	$table->string("name");
	$table->version("1715901859");
});

##
## token #
##
$schema->createTable("token", function(TableBuilder $table) {
	$table->string("name");
	$table->string("hash");
	$table->datetime("expiry");
	$table->integer("permissions");
	$table->integer("idUser");
	$table->version("1715901860");
});

##
## access #
##
$schema->createTable("access", function(TableBuilder $table) {
	$table->integer("permissions");
	$table->integer("idUser")->nullable();
	$table->integer("idGroup")->nullable();
	$table->integer("idProject");
	$table->version("1715901861");
});

##
## group2user #
##
$schema->createResolutionTable("group", "user", "0");

##
## project references #
##
$schema->changeTable("project", function(TableBuilder $table) {
	$table->addReference("user", "idUser", "idUser");
	$table->version("1715901858");
});

##
## token references #
##
$schema->changeTable("token", function(TableBuilder $table) {
	$table->addReference("user", "idUser", "idUser");
	$table->version("1715901860");
});

##
## access references #
##
$schema->changeTable("access", function(TableBuilder $table) {
	$table->addReference("user", "idUser", "idUser");
	$table->addReference("group", "idGroup", "idGroup");
	$table->addReference("project", "idProject", "idProject");
	$table->version("1715901861");
});