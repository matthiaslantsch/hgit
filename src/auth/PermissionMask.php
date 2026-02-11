<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\auth;

use holonet\common\BitwiseFlag;
use holonet\hgit\models\enum\Permission;

class PermissionMask extends BitwiseFlag {
	public function add(Permission $permission): void {
		$this->setFlag($permission->value, true);
	}

	public function allows(?Permission $permission): bool {
		if ($permission === null) {
			return $this->flags !== 0;
		}

		return $this->isFlagSet($permission->value);
	}

	public function remove(Permission $permission): void {
		$this->setFlag($permission->value, false);
	}

	public function visible(): bool {
		// just check if any permission bit is set
		return $this->flags !== 0;
	}
}
