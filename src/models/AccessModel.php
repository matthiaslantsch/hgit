<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\activerecord\attributes\Relation;
use holonet\activerecord\attributes\Table;
use holonet\activerecord\Model;
use holonet\activerecord\relations\BelongsTo;
use holonet\common\verifier\rules\Required;
use holonet\hgit\auth\PermissionMask;

#[Table('access')]
class AccessModel extends Model {
	#[Relation(forced: false)]
	public BelongsTo $group;

	#[Required]
	public PermissionMask $permissions;

	public BelongsTo $project;

	#[Relation(forced: false)]
	public BelongsTo $user;
}
