<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use holonet\common\BitwiseFlag;

/**
 * AccessMask class used to hold the permission integers and make it easily accessible.
 */
class AccessMask extends BitwiseFlag {
	/**
	 * Permission level constant for "admin"
	 * edit description / name, edit permissions.
	 * @var int ADMIN Permission constant
	 */
	public const ADMIN = 16;

	/**
	 * Permission level constant for "downloadArtifacts"
	 * download software artifacts.
	 * @var int DOWNLOADARTIFACTS Permission constant
	 */
	public const DOWNLOADARTIFACTS = 64;

	/**
	 * Permission level constant for "readCode"
	 * read code in the webgui/clone the repository.
	 * @var int READCODE Permission constant
	 */
	public const READCODE = 1;

	/**
	 * Permission level constant for "readFiles"
	 * access project files over the file browser.
	 * @var int READFILES Permission constant
	 */
	public const READFILES = 4;

	/**
	 * Permission level constant for "readWiki"
	 * access dokumentation.
	 * @var int READWIKI Permission constant
	 */
	public const READWIKI = 32;

	/**
	 * Permission level constant for "writeCode"
	 * push code via git/write wiki pages.
	 * @var int WRITECODE Permission constant
	 */
	public const WRITECODE = 2;

	/**
	 * Permission level constant for "writeFiles"
	 * write project files over the file browser.
	 * @var int WRITEFILES Permission constant
	 */
	public const WRITEFILES = 8;

	/**
	 * @param string $function Function string describing the function that the permission mask should be set to allow
	 */
	public function allow(string $function): void {
		switch ($function) {
			case 'readCode':
				$this->setFlag(self::READCODE, true);

				break;
			case 'writeCode':
				$this->setFlag(self::WRITECODE, true);

				break;
			case 'readFiles':
				$this->setFlag(self::READFILES, true);

				break;
			case 'writeFiles':
				$this->setFlag(self::WRITEFILES, true);

				break;
			case 'admin':
				$this->setFlag(self::ADMIN, true);

				break;
			case 'readWiki':
				$this->setFlag(self::READWIKI, true);

				break;
			case 'downloadArtifacts':
				$this->setFlag(self::DOWNLOADARTIFACTS, true);

				break;
		}
	}

	/**
	 * @param string $function Function string describing the function the user wants to access in that project
	 * @return bool true or false if the mask allows the given function or not
	 */
	public function doesAllow(string $function = 'see'): bool {
		switch ($function) {
			case 'see':
				//just check if any permission bit is set
				return $this->flags !== 0;
			case 'readCode':
				return $this->isFlagSet(self::READCODE);
			case 'writeCode':
				return $this->isFlagSet(self::WRITECODE);
			case 'readFiles':
				return $this->isFlagSet(self::READFILES);
			case 'writeFiles':
				return $this->isFlagSet(self::WRITEFILES);
			case 'admin':
				return $this->isFlagSet(self::ADMIN);
			case 'readWiki':
				return $this->isFlagSet(self::READWIKI);
			case 'downloadArtifacts':
				return $this->isFlagSet(self::DOWNLOADARTIFACTS);
			default:
				//just disallow it
				return false;
		}
	}
}
