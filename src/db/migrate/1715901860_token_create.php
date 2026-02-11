<?php
/**
 * This file is part of the holonet grabber archiving app
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\dbmigrate\builder\TableBuilder;
use holonet\dbmigrate\Migration;
use holonet\dbmigrate\Schema;

return new class implements Migration {
	public function down(Schema $schema): void {
		$schema->dropTable('token');
	}

	public function up(Schema $schema): void {
		$schema->createTable('token', static function (TableBuilder $t): void {
			$t->string('name');
			$t->string('hash');
			$t->datetime('expiry');
			$t->integer('permissions');
			$t->addReference('user');
		});
	}
};
