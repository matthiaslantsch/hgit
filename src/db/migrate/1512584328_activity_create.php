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
 * create the activity table.
 */
class ActivityCreateMigration extends Migration {
	/**
	 * migration into the down direction.
	 */
	public function down(): void {
		$this->schema->dropTable('activity');
	}

	/**
	 * migration into the up direction.
	 */
	public function up(): void {
		$this->schema->createTable('activity', static function (TableBuilder $t): void {
			$t->text('content');
			$t->timestamps();
			$t->addReference('user', 'idUser', 'idUser')->nullable();
			$t->addReference('project', 'idProject', 'idProject');
			$t->string('type', 10);
			$t->version('1512584328');
		});
	}
}
