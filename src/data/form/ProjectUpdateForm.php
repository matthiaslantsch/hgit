<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\data\form;

use holonet\hgit\models\enum\ProjectType;
use holonet\holofw\ProducesHttpResultsTrait;
use holonet\holofw\verifier\rules\UnsanitisedInput;

class ProjectUpdateForm {
	use ProducesHttpResultsTrait;

	#[UnsanitisedInput]
	public ProjectType $type;

	public function __construct(
		public string $description,
		string $type,
	) {
		$type = ProjectType::tryFrom($type);
		if ($type === null) {
			$this->unprocessableEntity(array('type' => 'Invalid project type'));
		}
		$this->type = $type;
	}
}
