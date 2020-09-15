<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\activerecord\ModelBase;
use holonet\hgit\helpers\AccessMask;
use holonet\activerecord\annotation\Table;
use holonet\activerecord\annotation\validate\Required;
use holonet\activerecord\annotation\relation\BelongsTo;

/**
 * UserAccessModel class to wrap around the "userAccess" database table.
 * @Table("userAccess")
 */
class UserAccessModel extends ModelBase {
	/**
	 * @Required
	 */
	protected AccessMask $mask;

	/**
	 * @BelongsTo("project")
	 */
	protected ProjectModel $project;

	/**
	 * @BelongsTo("user")
	 */
	protected UserModel $user;
}
