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

/**
 * GroupModel class to wrap around the "group" database table.
 */
class GroupModel extends ModelBase {
	/**
	 * @var array $hasMany Relationship mappings
	 */
	public static $hasMany = array('groupAccesses');

	/**
	 * @var array $many2many Array with many2many relationship mappings
	 */
	public static $many2many = array('users');

	/**
	 * @var array $validate Array with verification data for some of the columns
	 */
	public static $validate = array(
		'name' => array('presence', 'length' => array('min' => 3, 'max' => 60))
	);
}
