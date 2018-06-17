<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the activity table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the activity table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class ActivityCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('activity', function($t) {
			$t->text("content");
			$t->timestamps();
			$t->addReference("user", "idUser", "idUser")->nullable();
			$t->addReference("project", "idProject", "idProject");
			$t->addReference("activityType", "idActivityType", "idActivityType");
			$t->version("1512584328");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("activity");
	}

}
