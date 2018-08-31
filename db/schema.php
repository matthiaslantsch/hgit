<?php
# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.php definition is the main source for your
# database schema. To recreate the database, do not run all migrations, use the
# db/schema::load task
#
# It's strongly recommended that you check this file into your version control system.

use holonet\activerecord\Schema;

##
## projectType #
##
Schema::createTable("projectType", function($table) {
	$table->string("name", 40);
	$table->version("1512584324");
});

##
## project #
##
Schema::createTable("project", function($table) {
	$table->string("name", 20)->unique();
	$table->text("description")->nullable();
	$table->string("major", 10);
	$table->string("minor", 10);
	$table->string("fix", 10);
	$table->integer("otherMask");
	$table->integer("anyMask")->default(0);
	$table->integer("idUser");
	$table->integer("idProjectType")->nullable();
	$table->version("1512584326");
});

##
## activityType #
##
Schema::createTable("activityType", function($table) {
	$table->string("name", 10);
	$table->version("1512584327");
});

##
## activity #
##
Schema::createTable("activity", function($table) {
	$table->text("content");
	$table->integer("idUser")->nullable();
	$table->integer("idProject");
	$table->integer("idActivityType");
	$table->timestamps();
	$table->version("1512584328");
});

##
## issueType #
##
Schema::createTable("issueType", function($table) {
	$table->string("name", 10);
	$table->version("1512584329");
});

##
## issueStatus #
##
Schema::createTable("issueStatus", function($table) {
	$table->string("name", 10);
	$table->version("1512584330");
});

##
## issue #
##
Schema::createTable("issue", function($table) {
	$table->string("title", 40);
	$table->text("description");
	$table->string("targetVersion", 15)->nullable();
	$table->integer("idProject");
	$table->integer("author");
	$table->integer("idIssueType");
	$table->integer("idIssueStatus");
	$table->timestamps();
	$table->version("1512584331");
});

##
## comment #
##
Schema::createTable("comment", function($table) {
	$table->text("description");
	$table->integer("idIssue");
	$table->integer("idUser");
	$table->timestamps();
	$table->version("1512584332");
});

##
## group #
##
Schema::createTable("group", function($table) {
	$table->string("name", 60);
	$table->version("1512584333");
});

##
## userAccess #
##
Schema::createTable("userAccess", function($table) {
	$table->integer("mask");
	$table->integer("idProject");
	$table->integer("idUser");
	$table->version("1512584335");
});

##
## groupAccess #
##
Schema::createTable("groupAccess", function($table) {
	$table->integer("mask");
	$table->integer("idProject");
	$table->integer("idGroup");
	$table->version("1512584336");
});

##
## user #
##
Schema::createTable("user", function($table) {
	$table->string("name", 40);
	$table->string("nick", 20);
	$table->string("email", 60)->nullable();
	$table->version("1535368976");
});

##
## group2user #
##
Schema::createResolutionTable("group", "user", "1512584334");

##
## project references #
##
Schema::changeTable("project", function($table) {
	$table->addReference("user", "idUser", "idUser");
	$table->addReference("projectType", "idProjectType", "idProjectType");
	$table->version("1512584326");
});

##
## activity references #
##
Schema::changeTable("activity", function($table) {
	$table->addReference("user", "idUser", "idUser");
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("activityType", "idActivityType", "idActivityType");
	$table->version("1512584328");
});

##
## issue references #
##
Schema::changeTable("issue", function($table) {
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("user", "author", "idUser");
	$table->addReference("issueType", "idIssueType", "idIssueType");
	$table->addReference("issueStatus", "idIssueStatus", "idIssueStatus");
	$table->version("1512584331");
});

##
## comment references #
##
Schema::changeTable("comment", function($table) {
	$table->addReference("issue", "idIssue", "idIssue");
	$table->addReference("user", "idUser", "idUser");
	$table->version("1512584332");
});

##
## userAccess references #
##
Schema::changeTable("userAccess", function($table) {
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("user", "idUser", "idUser");
	$table->version("1512584335");
});

##
## groupAccess references #
##
Schema::changeTable("groupAccess", function($table) {
	$table->addReference("project", "idProject", "idProject");
	$table->addReference("group", "idGroup", "idGroup");
	$table->version("1512584336");
});