<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Model class for the GroupAccessModel model
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\common as co;

/**
 * GroupAccessModel class to wrap around the "groupAccess" database table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class GroupAccessModel extends AccessMaskModel {

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access public
	 * @var	array with relationships
	 */
	public static $belongsTo = array("group", "project");

}
