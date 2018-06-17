<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Model class for the GroupModel model
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\common as co;
use holonet\activerecord\ModelBase;

/**
 * GroupModel class to wrap around the "group" database table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class GroupModel extends ModelBase {

	/**
	 * property containing many2many relationship mappings
	 *
	 * @access public
	 * @var	array with relationships
	 */
	public static $many2many = array("users");

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access public
	 * @var	array with relationships
	 */
	public static $hasMany = array("groupAccesses");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var	array with verification data
	 */
	public static $validate = array(
		"name" => array("presence", "length" => array("min" => 3, "max" => 60))
	);

}
