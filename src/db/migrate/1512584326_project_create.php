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
 * create the project table.
 */
class ProjectCreateMigration extends Migration {
	/**
	 * migration into the down direction.
	 */
	public function down(): void {
		$this->schema->dropTable('project');
	}

	/**
	 * migration into the up direction.
	 */
	public function up(): void {
		$this->schema->createTable('project', static function (TableBuilder $t): void {
			$t->string('name', 20)->unique();
			$t->text('description')->nullable();
			$t->string('major', 10);
			$t->string('minor', 10);
			$t->string('fix', 10);
			$t->integer('otherMask');
			$t->integer('anyMask')->default(0);
			$t->addReference('user');
			$t->string('type', 10);
			$t->version('1512584326');
		});
	}
}
