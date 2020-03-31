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
 * create the issue table.
 */
class IssueCreateMigration extends Migration {
	/**
	 * migration into the down direction.
	 */
	public function down(): void {
		$this->schema->dropTable('issue');
	}

	/**
	 * migration into the up direction.
	 */
	public function up(): void {
		$this->schema->createTable('issue', static function (TableBuilder $t): void {
			$t->string('title', 40);
			$t->text('description');
			$t->string('targetVersion', 15)->nullable();
			$t->addReference('project', 'idProject', 'idProject');
			$t->addReference('user', 'author', 'idUser');
			$t->string('type', 10);
			$t->string('status', 10);
			$t->timestamps();
			$t->version('1512584331');
		});
	}
}
