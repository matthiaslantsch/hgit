<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the issueType table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the issueType table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class IssueTypeCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('issueType', function($t) {
			$t->string("name")->size('10');
			$t->version("1512584329");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("issueType");
	}

}