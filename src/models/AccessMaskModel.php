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
 * AccessMask class used to hold common logic for both access mask models.
 *
 * @author  matthias.lantsch
 */
class AccessMaskModel extends ModelBase {
	/**
	 * @var array $validate Array with verification data for some of the columns
	 */
	public static $validate = array(
		'mask' => array('presence')
	);

	/**
	 * property containing a reference to the AccessMask object that represents
	 * the internal access mask integer.
	 * @var AccessMask $accessmask The internal mask represented by an object
	 */
	private $accessmask;

	/**
	 * function returning a helper AccessMask object representing the internal
	 * mask so we can work with it more easily
	 * will be called by the activerecord base model class if the mask property
	 * is accessed.
	 * @return AccessMask object representing the internal integer
	 */
	public function getMask(): AccessMask {
		if ($this->accessmask === null) {
			$this->accessmask = new AccessMask($this->readAttribute('mask'));
		}

		return $this->accessmask;
	}

	/**
	 * overwritten save method used to save the access mask integer if it changed.
	 * @return bool on success or not
	 */
	public function save(): bool {
		if ($this->readAttribute('mask') !== $this->accessmask->flags) {
			//get the new integer into the property of the model
			$this->mask = $this->accessmask->flags;
		}

		return parent::save();
	}
}
