<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\dbmigrate\Migration;
use holonet\dbmigrate\builder\TableBuilder;

/**
 * drop version fields.
 */
class ProjectDropVersionFieldsMigration extends Migration {
	public function down(): void {
		$this->schema->changeTable('project', static function (TableBuilder $t): void {
			$t->string('major', 10);
			$t->string('minor', 10);
			$t->string('fix', 10);
		});
	}

	public function up(): void {
		$this->schema->changeTable('project', static function (TableBuilder $t): void {
			$t->dropColumn('major');
			$t->dropColumn('minor');
			$t->dropColumn('fix');
		});
	}
}
