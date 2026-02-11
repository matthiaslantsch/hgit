<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use DateTime;
use holonet\activerecord\attributes\Table;
use holonet\activerecord\Model;
use holonet\activerecord\relations\BelongsTo;
use holonet\common\verifier\rules\Required;
use holonet\common\verifier\rules\string\MaxLength;
use holonet\hgit\auth\PermissionMask;

#[Table('token')]
class TokenModel extends Model {
	#[Required]
	public DateTime $expiry;

	#[MaxLength(255)]
	#[Required]
	public string $hash;

	#[MaxLength(255)]
	#[Required]
	public string $name;

	#[Required]
	public PermissionMask $permissions;

	public BelongsTo $user;
}
