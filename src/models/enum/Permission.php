<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models\enum;

enum Permission: int {
	/**
	 * Permission level constant for "admin"
	 * edit description / name, edit permissions.
	 */
	case ADMIN = 32;

	/**
	 * Permission level constant for "buildArtifacts"
	 * Rebuild artifacts for a project.
	 */
	case BUILDARTIFACTS = 8;

	/**
	 * Permission level constant for "downloadArtifacts"
	 * download software artifacts.
	 */
	case DOWNLOADARTIFACTS = 16;

	/**
	 * Permission level constant for "readCode"
	 * read code in the webgui/clone the repository.
	 */
	case READCODE = 1;

	/**
	 * Permission level constant for "readWiki"
	 * access dokumentation.
	 */
	case READWIKI = 4;

	/**
	 * Permission level constant for "writeCode"
	 * push code via git/write wiki pages.
	 */
	case WRITECODE = 2;
}
