<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\activerecord\sets\ModelSet;
use holonet\activerecord\annotation\Table;
use holonet\holofw\auth\StandardUserModelTrait;
use holonet\activerecord\annotation\validate\Length;
use holonet\activerecord\annotation\relation\HasMany;
use holonet\activerecord\annotation\validate\Required;
use holonet\activerecord\annotation\relation\Many2Many;

/**
 * UserModel class to wrap around the "user" database table.
 * @Table("user")
 */
class UserModel extends \holonet\holofw\auth\UserModel {
	use StandardUserModelTrait;

	/**
	 * @Length(max="60")
	 */
	protected ?string $email;

	/**
	 * @Required
	 */
	protected string $externalid;

	/**
	 * @Many2Many("groups")
	 * @var GroupModel[]|ModelSet $groups
	 */
	protected ModelSet $groups;

	/**
	 * @Required
	 * @Length(min="4", max="20")
	 */
	protected string $nick;

	/**
	 * @HasMany("projects")
	 * @var ModelSet|ProjectModel[] $projects
	 */
	protected ModelSet $projects;

	/**
	 * @HasMany("userAccesses")
	 * @var ModelSet|UserAccessModel[] $userAccesses
	 */
	protected ModelSet $userAccesses;

	/**
	 * {@inheritDoc}
	 */
	public static function supportedUserClaims(): array {
		return array(
			'username' => 'nick',
			'email' => 'email'
		);
	}
}
