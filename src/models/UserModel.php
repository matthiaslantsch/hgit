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
use holonet\holofw\auth\UserModelInterface;
use holonet\holofw\auth\StandardUserModelTrait;

/**
 * UserModel class to wrap around the "user" database table.
 */
class UserModel extends ModelBase implements UserModelInterface {
	use StandardUserModelTrait;

	/**
	 * @var array $hasMany Relationship mappings
	 */
	public static $hasMany = array('projects', 'userAccesses');

	/**
	 * @var array $many2many Array with many2many relationship mappings
	 */
	public static $many2many = array('groups');

	/**
	 * @var array $validate Array with verification data for some of the columns
	 */
	public static $validate = array(
		'nick' => array('presence', 'length' => array('min' => 4, 'max' => 20)),
		'email' => array('length' => array('max' => 60))
	);

	/**
	 * {@inheritdoc}
	 */
	public static function supportedUserClaims(): array {
		return array(
			'username' => 'nick',
			'email' => 'email'
		);
	}
}
