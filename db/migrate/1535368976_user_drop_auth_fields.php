<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to drop the auth fields from the user table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * change the user table
 * drop the auth fields because of the sphinx integration
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class UserDropAuthFieldsMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable('user', function($t) {
			$t->dropColumn("authhash");
			$t->dropColumn("authdigest");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable('user', function($t) {
			$t->string("authhash")->nullable();
			$t->string("authdigest")->nullable();
		});
	}

}
