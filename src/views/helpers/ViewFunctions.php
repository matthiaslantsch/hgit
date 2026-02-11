<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\views\helpers;

use Highlight\Highlighter;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\ProjectModel;
use holonet\holofw\session\User;
use Parsedown;

class ViewFunctions {
	public static function can(ProjectModel $project, Permission $permission, ?User $user): bool {
		if ($project->publicPermissions->allows($permission)) {
			return true;
		}

		if ($user === null) {
			return false;
		}

		if ($project->internalPermissions->allows($permission)) {
			return true;
		}

		return ($user->usermodel->effectivePermissions()[$project->id] ?? null)?->allows($permission) ?? false;
	}

	/**
	 * small helper function using the highlight.js php port to render a code pre tag.
	 * @param string $code The code to be highlighted
	 * @param string $lang The language to highlight with
	 * @throws Exception
	 * @return string html markup with highlighted code inside <pre><code> tags
	 */
	public static function highlightCode(string $code, ?string $lang = null): string {
		$hl = new Highlighter();
		if ($lang !== null) {
			$r = $hl->highlight($lang, $code);
		} else {
			$r = $hl->highlightAuto($code);
		}

		return "<pre><code class=\"hljs {$r->language}\">{$r->value}</code></pre>";
	}

	public static function renderMarkdown(string $markdown): string {
		$parsedown = new Parsedown();

		return $parsedown->text($markdown);
	}
}
