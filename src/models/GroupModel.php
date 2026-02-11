<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\activerecord\attributes\Table;
use holonet\activerecord\attributes\Unique;
use holonet\activerecord\Model;
use holonet\activerecord\relations\Many2Many;
use holonet\common\verifier\rules\Required;
use holonet\common\verifier\rules\string\MaxLength;

#[Table('group')]
class GroupModel extends Model {
	#[MaxLength(40)]
	#[Required]
	#[Unique('Choose a different name')]
	public string $name;

	public Many2Many $users;
}
