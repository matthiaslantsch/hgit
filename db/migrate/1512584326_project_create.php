<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the project table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the project table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class ProjectCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('project', function($t) {
			$t->string("name")->size('20')->unique();
			$t->text("description")->nullable();
			$t->string("major")->size('10');
			$t->string("minor")->size('10');
			$t->string("fix")->size('10');
			$t->integer("otherMask");
			$t->integer("anyMask")->default('0');
			$t->addReference("user");
			$t->addReference("projectType")->nullable();
			$t->version("1512584326");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("project");
	}

}
