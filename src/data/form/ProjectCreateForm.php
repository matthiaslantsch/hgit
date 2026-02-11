<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\data\form;

use holonet\hgit\auth\PermissionMask;
use holonet\hgit\models\enum\PermissionPreset;
use holonet\hgit\models\enum\ProjectType;
use holonet\holofw\auth\UserModel;
use holonet\holofw\ProducesHttpResultsTrait;
use holonet\holofw\verifier\rules\UnsanitisedInput;

class ProjectCreateForm {
	use ProducesHttpResultsTrait;

	#[UnsanitisedInput]
	public PermissionMask $internalPermissions;

	#[UnsanitisedInput]
	public PermissionMask $publicPermissions;

	public string $slug;

	#[UnsanitisedInput]
	public ProjectType $type;

	#[UnsanitisedInput]
	protected PermissionPreset $preset;

	public function __construct(
		public string $name,
		#[UnsanitisedInput]
		public UserModel $maintainer,
		string $type,
		string $permissionPreset,
		public string $description = '',
	) {
		$type = ProjectType::tryFrom($type);
		if ($type === null) {
			$this->unprocessableEntity(array('type' => 'Invalid project type'));
		}
		$this->type = $type;

		$permissionPreset = PermissionPreset::tryFrom($permissionPreset);
		if ($permissionPreset === null) {
			$this->unprocessableEntity(array('permissionPreset' => 'Invalid permission preset'));
		}

		$this->publicPermissions = new PermissionMask($permissionPreset->publicPermissions());
		$this->internalPermissions = new PermissionMask($permissionPreset->internalPermissions());
		$this->preset = $permissionPreset;

		$this->slug = $this->generateSlug();
	}

	public function preset(): PermissionPreset {
		return $this->preset;
	}

	private function generateSlug(): string {
		$accentsReplacements = array(
			'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
			'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
			'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
			'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'
		);
		$slug = strtr($this->name, $accentsReplacements);

		// trim leading or trailing whitespace
		$slug = trim($slug);
		// convert to lowercase
		$slug = strtolower($slug);
		// replace slashes with hyphens
		$slug = str_replace('/', '-', $slug);
		// remove non-alphanumeric characters
		$slug = preg_replace('/[^a-z0-9 -]/', '', $slug);
		// replace spaces with hyphens
		$slug = str_replace(' ', '-', $slug);

		// remove consecutive hyphens
		return preg_replace('/-+/', '-', $slug);
	}
}
