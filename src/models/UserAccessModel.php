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
 * UserAccessModel class to wrap around the "userAccess" database table.
 */
class UserAccessModel extends AccessMaskModel {
	/**
	 * @var array $belongsTo Array with definitions for a belongsTo relationship
	 */
	public static $belongsTo = array('user', 'project');
}
