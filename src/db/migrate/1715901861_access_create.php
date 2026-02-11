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
		$schema->dropTable('access');
	}

	public function up(Schema $schema): void {
		$schema->createTable('access', static function (TableBuilder $t): void {
			$t->integer('permissions');
			$t->addReference('user')->nullable();
			$t->addReference('group')->nullable();
			$t->addReference('project');
		});
	}
};
