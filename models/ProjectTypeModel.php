<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Model class for the ProjectType model
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\common as co;
use holonet\activerecord\ModelBase;

/**
 * ProjectType class to wrap around the "projectType" database table
 * contains different type names of projects
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class ProjectTypeModel extends ModelBase {

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access public
	 * @var    array $hasMany Relationship mappings
	 */
	public static $hasMany = array("projects");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
	 */
	public static $validate = array(
		"name" => array("presence", "length" => array("max" => 40))
	);

}
