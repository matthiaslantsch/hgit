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
use holonet\activerecord\sets\ModelSet;
use holonet\activerecord\annotation\Table;
use holonet\activerecord\annotation\validate\Length;
use holonet\activerecord\annotation\relation\HasMany;
use holonet\activerecord\annotation\validate\Required;
use holonet\activerecord\annotation\relation\Many2Many;

/**
 * GroupModel class to wrap around the "group" database table.
 * @Table("group")
 */
class GroupModel extends ModelBase {
	/**
	 * @HasMany("groupAccesses")
	 * @var GroupAccessModel[]|ModelSet $groupAccesses
	 */
	protected ModelSet $groupAccesses;

	/**
	 * @Required
	 * @Length(min="3", max="60")
	 */
	protected string $name;

	/**
	 * @Many2Many("users")
	 * @var ModelSet|UserModel[] $users
	 */
	protected ModelSet $users;
}
