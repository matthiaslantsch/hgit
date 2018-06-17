<?php
/**
 * This file is part of the holonet project management tool
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the issue table
 *
 * @package holonet project management tool
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the issue table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\db\migrate
 */
class IssueCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('issue', function($t) {
			$t->string("title")->size('40');
			$t->text("description");
			$t->string("targetVersion")->size('15')->nullable();
			$t->addReference("project", "idProject", "idProject");
			$t->addReference("user", "author", "idUser");
			$t->addReference("issueType", "idIssueType", "idIssueType");
			$t->addReference("issueStatus", "idIssueStatus", "idIssueStatus");
			$t->timestamps();
			$t->version("1512584331");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("issue");
	}

}