<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the AccessMask mask mapping class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use holonet\common as co;
use holonet\common\BitwiseFlag;

/**
 * AccessMask class used to hold the permission integers and make it easily accessible
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\helpers
 */
class AccessMask extends BitwiseFlag{

	/**
	 * Permission level constant for "readCode"
	 * read code in the webgui/clone the repository
	 *
	 * @access public
	 * @var    int READCODE Permission constant
	 */
	const READCODE = 1;

	/**
	 * Permission level constant for "writeCode"
	 * push code via git/write wiki pages
	 *
	 * @access public
	 * @var    int WRITECODE Permission constant
	 */
	const WRITECODE = 2;

	/**
	 * Permission level constant for "readFiles"
	 * access project files over the file browser
	 *
	 * @access public
	 * @var    int READFILES Permission constant
	 */
	const READFILES = 4;

	/**
	 * Permission level constant for "writeFiles"
	 * write project files over the file browser
	 *
	 * @access public
	 * @var    int WRITEFILES Permission constant
	 */
	const WRITEFILES = 8;

	/**
	 * Permission level constant for "admin"
	 * edit description / name, edit permissions
	 *
	 * @access public
	 * @var    int ADMIN Permission constant
	 */
	const ADMIN = 16;

	/**
	 * Permission level constant for "readWiki"
	 * access dokumentation
	 *
	 * @access public
	 * @var    int READWIKI Permission constant
	 */
	const READWIKI = 32;

	/**
	 * Permission level constant for "downloadArtifacts"
	 * download software artifacts
	 *
	 * @access public
	 * @var    int DOWNLOADARTIFACTS Permission constant
	 */
	const DOWNLOADARTIFACTS = 64;

	/**
	 * small helper function letting the use set permissions in our mask via convenient methods
	 *
	 * @access public
	 * @param  string $function Function string decribing the function that the permission mask should be set to allow
	 * @return void
	 */
	public function allow($function) {
		switch ($function) {
			case "readCode":
				return $this->setFlag(self::READCODE, true);
			case "writeCode":
				return $this->setFlag(self::WRITECODE, true);
			case "readFiles":
				return $this->setFlag(self::READFILES, true);
			case "writeFiles":
				return $this->setFlag(self::WRITEFILES, true);
			case "admin":
				return $this->setFlag(self::ADMIN, true);
			case "readWiki":
				return $this->setFlag(self::READWIKI, true);
			case "downloadArtifacts":
				return $this->setFlag(self::DOWNLOADARTIFACTS, true);
		}
	}

	/**
	 * small helper function creating and returning a mapping array from function names
	 * to permission integers
	 *
	 * @access public
	 * @param  string $function Function string decribing the function the user wants to access in that project
	 * @return boolean true or false if the mask allows a function or not
	 */
	public function doesAllow($function = "see") {
		switch ($function) {
			case "see":
				//just check if any permission bit is set
				return $this->flags != 0;
			case "readCode":
				return $this->isFlagSet(self::READCODE);
			case "writeCode":
				return $this->isFlagSet(self::WRITECODE);
			case "readFiles":
				return $this->isFlagSet(self::READFILES);
			case "writeFiles":
				return $this->isFlagSet(self::WRITEFILES);
			case "admin":
				return $this->isFlagSet(self::ADMIN);
			case "readWiki":
				return $this->isFlagSet(self::READWIKI);
			case "downloadArtifacts":
				return $this->isFlagSet(self::DOWNLOADARTIFACTS);
			default:
				//just disallow it
				return false;
		}
	}

}
