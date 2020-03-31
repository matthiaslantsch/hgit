<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 */

namespace holonet\hgit\models\enum;

use MyCLabs\Enum\Enum;

/**
 * @method static ProjectType PHP_LIBRARY()
 * @method static ProjectType PHP_APP()
 * @method static ProjectType HOLOFW_APP()
 * @method static ProjectType OTHER()
 * @psalm-immutable
 */
class ProjectType extends Enum {
	private const HOLOFW_APP = 'holofw app';

	private const OTHER = 'other';

	private const PHP_APP = 'php app';

	private const PHP_LIBRARY = 'php library';
}
