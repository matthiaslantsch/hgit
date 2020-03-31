<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\dbmigrate\Migration;
use holonet\dbmigrate\builder\TableBuilder;

/**
 * create the groupAccess table.
 */
class GroupAccessCreateMigration extends Migration {
	/**
	 * migration into the down direction.
	 */
	public function down(): void {
		$this->schema->dropTable('groupAccess');
	}

	/**
	 * migration into the up direction.
	 */
	public function up(): void {
		$this->schema->createTable('groupAccess', static function (TableBuilder $t): void {
			$t->integer('mask');
			$t->addReference('project');
			$t->addReference('group');
			$t->version('1512584335');
		});
	}
}
