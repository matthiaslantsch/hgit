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
 * create the comment table.
 */
class CommentCreateMigration extends Migration {
	/**
	 * migration into the down direction.
	 */
	public function down(): void {
		$this->schema->dropTable('comment');
	}

	/**
	 * migration into the up direction.
	 */
	public function up(): void {
		$this->schema->createTable('comment', static function (TableBuilder $t): void {
			$t->text('description');
			$t->timestamps();
			$t->addReference('issue', 'idIssue', 'idIssue');
			$t->addReference('user', 'idUser', 'idUser');
			$t->version('1512584332');
		});
	}
}
