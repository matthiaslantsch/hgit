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

/**
 * ProjectModel class to wrap around the "project" database table.
 */
class ProjectModel extends ModelBase {
	/**
	 * @var array $belongsTo Array with definitions for a belongsTo relationship
	 */
	public static $belongsTo = array('user');

	/**
	 * @var array $hasMany Relationship mappings
	 */
	public static $hasMany = array('groupAccesses', 'userAccesses');

	/**
	 * @var array $validate Array with verification data for some of the columns
	 */
	public static $validate = array(
		'name' => array('presence', 'length' => array('max' => 20), 'uniqueness'),
		'type' => array('presence'),
		'major' => array('presence'),
		'minor' => array('presence'),
		'fix' => array('presence'),
		'otherMask' => array('presence')
	);

	/**
	 * property containing a reference to the AccessMask object that represents
	 * the internal access mask integer.
	 * @var AccessMask $anymaskObj The internal mask represented by an object
	 */
	private $anymaskObj;

	/**
	 * property containing a reference to the AccessMask object that represents
	 * the internal access mask integer.
	 * @var AccessMask $othermaskObj The internal mask represented by an object
	 */
	private $othermaskObj;

	/**
	 * {@inheritdoc}
	 * Overwritten so we can replace permission presets.
	 */
	public function __construct($data = array(), $fromDb = false) {
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

		parent::__construct($data, $fromDb);
	}

	/**
	 * function returning a helper AccessMask object representing the internal
	 * mask so we can work with it more easily
	 * will be called by the activerecord base model class if the anyMask property
	 * is accessed.
	 * @return AccessMask object representing the internal integer
	 */
	public function getAnyMask(): AccessMask {
		if ($this->anymaskObj === null) {
			$this->anymaskObj = new AccessMask($this->readAttribute('anyMask'));
		}

		return $this->anymaskObj;
	}

	/**
	 * function returning a helper AccessMask object representing the internal
	 * mask so we can work with it more easily
	 * will be called by the activerecord base model class if the otherMask property
	 * is accessed.
	 * @return AccessMask object representing the internal integer
	 */
	public function getOtherMask(): AccessMask {
		if ($this->othermaskObj === null) {
			$this->othermaskObj = new AccessMask($this->readAttribute('otherMask'));
		}

		return $this->othermaskObj;
	}

	/**
	 * overwritten save method used to save the access mask integer if it changed.
	 */
	public function save(): bool {
		if ($this->othermaskObj !== null && $this->readAttribute('otherMask') !== $this->othermaskObj->flags) {
			//get the new integer into the property of the model
			$this->othermask = $this->othermaskObj->flags;
		}

		if ($this->anymaskObj !== null && $this->readAttribute('anyMask') !== $this->anymaskObj->flags) {
			//get the new integer into the property of the model
			$this->anyMask = $this->anymaskObj->flags;
		}

		if ($this->valid() !== true) {
			return false;
		}

		return parent::save();
	}

	/**
	 * small helper function used to "sluggify" the project name
	 * turns vendor/package into vendor-package.
	 * @return string with the sluggified project name
	 */
	public function slugname(): string {
		return str_replace('/', '-', $this->name);
	}

	/**
	 * @return string a fixed version string
	 */
	public function version(): string {
		return sprintf('%s.%s.%s', $this->major, $this->minor, $this->fix);
	}
}
