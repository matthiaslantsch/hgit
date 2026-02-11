<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models\enum;

enum PermissionPreset: string {
	case INTERNAL = 'internal';
	case PRIVATE = 'private';
	case PUBLIC = 'public';

	public function description(): string {
		return match ($this) {
			self::PUBLIC => 'Everyone can read/access code and documentation, write access is limited to authenticated users.',
			self::INTERNAL => 'The project can only be accessed by authenticated users.',
			self::PRIVATE => 'Access to the project has to be granted to every user manually.',
		};
	}

	public function internalPermissions(): int {
		return match ($this) {
			// hide the project from prying eyes
			self::PUBLIC, self::PRIVATE => 0,
			// allow authenticated users to read code and wiki
			self::INTERNAL => Permission::READCODE->value | Permission::READWIKI->value | Permission::WRITECODE->value | Permission::BUILDARTIFACTS->value | Permission::DOWNLOADARTIFACTS->value
		};
	}

	public function publicPermissions(): int {
		return match ($this) {
			// allow anyone to read code and wiki
			self::PUBLIC => Permission::READCODE->value | Permission::READWIKI->value | Permission::DOWNLOADARTIFACTS->value,
			// disallow everything to the public
			self::INTERNAL, self::PRIVATE => 0,
		};
	}
}
