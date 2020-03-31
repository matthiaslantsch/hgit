<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

/**
 * GroupAccessModel class to wrap around the "groupAccess" database table.
 */
class GroupAccessModel extends AccessMaskModel {
	/**
	 * @var array $belongsTo Relationship mappings
	 */
	public static $belongsTo = array('group', 'project');
}
