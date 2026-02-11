<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\utils;

use holonet\hgit\models\enum\ProjectType;
use holonet\holofw\InteractsWithSessionTrait;

trait VisibleProjectsOptionsTrait {
	use InteractsWithSessionTrait;

	protected function projectIndexOptions(?ProjectType $typeFilter = null): array {
		if ($this->hasSessionUser() && !empty($permissions = $this->getAppUser()->effectivePermissions())) {
			$options = array('idProject' => array_keys($permissions));
		} else {
			$options = array('publicPermissions[!]' => 0);
		}

		if ($typeFilter !== null) {
			$options['type'] = $typeFilter;
		}

		return $options;
	}
}
