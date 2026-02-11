<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models\enum;

enum ProjectType: string {
	case HOLOFW_APP = 'holofw app';
	case OTHER = 'other';
	case PHP_APP = 'php app';
	case PHP_LIBRARY = 'php library';
}
