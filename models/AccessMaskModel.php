<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Model class for the AccessMask model base class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\activerecord\ModelBase;
use holonet\hiboard\helpers\AccessMask;

/**
 * AccessMask class used to hold common logic for both access mask models
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class AccessMaskModel extends ModelBase {

	/**
	 * property containing a reference to the AccessMask object that represents
	 * the internal access mask integer
	 *
	 * @access private
	 * @var	   AccessMask $accessmask The internal mask represented by an object
	 */
	 private $accessmask;

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array with verification data
	 */
	public static $validate = array(
		"mask" => array("presence")
	);

	/**
	 * function returning a helper AccessMask object representing the internal
	 * mask so we can work with it more easily
	 * will be called by the activerecord base model class if the mask property
	 * is accessed
	 *
	 * @access public
	 * @return AccessMask object representing the internal integer
	 */
	public function getMask() {
		if($this->accessmask === null) {
			$this->accessmask = new AccessMask($this->readAttribute("mask"));
		}
		return $this->accessmask;
	}

	/**
	 * overwritten save method used to save the access mask integer if it changed
	 *
	 * @access public
	 * @return boolean on success or not
	 */
	public function save() {
		if($this->readAttribute("mask") != $this->accessmask->mask) {
			//get the new integer into the property of the model
			$this->mask = $this->accessmask->mask;
		}

		return parent::save();
	}

}
