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
use holonet\activerecord\attributes\Unique;
use holonet\activerecord\Model;
use holonet\activerecord\relations\BelongsTo;
use holonet\common\verifier\rules\Required;
use holonet\common\verifier\rules\string\MaxLength;
use holonet\hgit\auth\PermissionMask;
use holonet\hgit\models\enum\ProjectType;

#[Table('project')]
class ProjectModel extends Model {
	public ?string $description = null;

	#[Required]
	public PermissionMask $internalPermissions;

	#[Relation(fkey: 'idUser', references: 'user.idUser', class: UserModel::class)]
	#[Required]
	public BelongsTo $maintainer;

	#[MaxLength(40)]
	#[Required]
	#[Unique('Choose a different name')]
	public string $name;

	#[Required]
	public PermissionMask $publicPermissions;

	#[MaxLength(40)]
	#[Required]
	#[Unique('Choose a different name')]
	public string $slug;

	#[Required]
	public ProjectType $type;
}
