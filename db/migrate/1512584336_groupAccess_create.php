<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the groupAccess table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the groupAccess table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class GroupAccessCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('groupAccess', function($t) {
			$t->integer("mask");
			$t->addReference("project");
			$t->addReference("group");
			$t->version("1512584335");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("groupAccess");
	}

}
