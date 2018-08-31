<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Model class for the UserModel model
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\common as co;
use holonet\activerecord\ModelBase;

/**
 * UserModel class to wrap around the "user" database table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class UserModel extends ModelBase {

	/**
	 * property containing many2many relationship mappings
	 *
	 * @access public
	 * @var	array with relationships
	 */
	public static $many2many = array("groups");

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access public
	 * @var	array with relationships
	 */
	public static $hasMany = array("projects", "userAccesses");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var	array with verification data
	 */
	public static $validate = array(
		"nick" => array("presence", "uniqueness", "length" => array("min" => 4, "max" => 20)),
		"name" => array("presence", "length" => array("min" => 4, "max" => 40)),
		"email" => array("length" => array("max" => 60))
	);

	/**
	 * constructor method taking a assotiative array as an argument
	 * overwritten for hooking into the creation process
	 *
	 * @access public
	 * @param  array $data Data the assotiative array
	 * @param  boolean $fromDb Boolean marking the data entry as new or not
	 * @return void
	 */
	public function __construct($data = array(), $fromDb = false) {
		if(isset($data['password'])) {
			//auth hash for use with password_verify
			$data['authhash'] = password_hash($data['password'], PASSWORD_DEFAULT);
			//digest hash with password and username
			$data['authdigest'] = md5("{$data['name']}:hgit:{$data["password"]}");
			unset($data['password']);
		}
		parent::__construct($data, $fromDb);
	}

}
