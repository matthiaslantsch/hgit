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
use holonet\activerecord\sets\ModelSet;
use holonet\hgit\models\enum\ProjectType;
use holonet\activerecord\annotation\Table;
use holonet\activerecord\annotation\validate\Length;
use holonet\activerecord\annotation\validate\Unique;
use holonet\activerecord\annotation\relation\HasMany;
use holonet\activerecord\annotation\validate\Required;
use holonet\activerecord\annotation\relation\BelongsTo;

/**
 * ProjectModel class to wrap around the "project" database table.
 * @Table("project")
 */
class ProjectModel extends ModelBase {
	/**
	 * @Required
	 */
	protected AccessMask $anyMask;

	protected ?string $description;

	/**
	 * @HasMany("groupAccesses")
	 * @var GroupAccessModel[]|ModelSet $groupAccesses
	 */
	protected ModelSet $groupAccesses;

	/**
	 * @Required
	 * @Unique
	 * @Length(max="20")
	 */
	protected string $name;

	/**
	 * @Required
	 */
	protected AccessMask $otherMask;

	/**
	 * @Required
	 */
	protected ProjectType $type;

	/**
	 * @BelongsTo("user")
	 */
	protected UserModel $user;

	/**
	 * @HasMany("userAccesses")
	 * @var ModelSet|UserAccessModel[] $userAccesses
	 */
	protected ModelSet $userAccesses;

	/**
	 * {@inheritDoc}
	 * Overwritten so we can replace permission presets.
	 */
	public function __construct($data = array(), $fromDb = false, ?\holonet\activerecord\Table $table = null) {
		if (isset($data['permissionPreset'])) {
			switch ($data['permissionPreset']) {
				case 'public':
					//allow anyone to read code and wiki
					$data['anyMask'] = AccessMask::READCODE | AccessMask::READWIKI;
					//allow authenticated users to write code and wiki as well as read project files
					$data['otherMask'] = AccessMask::READCODE | AccessMask::READWIKI
						| AccessMask::WRITECODE | AccessMask::WRITEFILES | AccessMask::READFILES;

					break;
				case 'internal':
					//allow only internal users to write code and wiki as well as read project files
					$data['otherMask'] = AccessMask::READCODE | AccessMask::READWIKI
						| AccessMask::WRITECODE | AccessMask::WRITEFILES | AccessMask::READFILES;

					break;
				default:
					//set it to private
					$data['otherMask'] = 0;

					break;
			}

			unset($data['permissionPreset']);
		}

		$data['anyMask'] ??= 0;

		parent::__construct($data, $fromDb, $table);
	}

	/**
	 * small helper function used to "sluggify" the project name
	 * turns vendor/package into vendor-package.
	 * @return string with the sluggified project name
	 */
	public function slugname(): string {
		return str_replace('/', '-', $this->name);
	}
}
