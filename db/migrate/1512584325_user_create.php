<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the user table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the user table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class UserCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('user', function($t) {
			$t->string("name")->size('40');
			$t->string("nick")->size('20');
			$t->string("email")->size('60')->nullable();
			$t->string("authhash")->nullable();
			$t->string("authdigest")->nullable();
			$t->version("1512584325");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("user");
	}

}
