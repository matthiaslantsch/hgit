<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\views\helpers;

use Exception;
use Parsedown;
use Highlight\Highlighter;
use holonet\holofw\session\User;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\HgitAuthoriser;

class ViewUtils extends \holonet\holofw\viewhelpers\ViewUtils {
	/**
	 * small helper function using the highlight.js php port to render a code pre tag.
	 * @param string $code The code to be highlighted
	 * @param string $lang The language to highlight with
	 * @throws Exception
	 * @return string html markup with highlighted code inside <pre><code> tags
	 */
	public static function highlightCode(string $code, string $lang = null) {
		$hl = new Highlighter();
		if ($lang !== null) {
			$r = $hl->highlight($lang, $code);
		} else {
			$r = $hl->highlightAuto($code);
		}

		return "<pre><code class=\"hljs {$r->language}\">{$r->value}</code></pre>";
	}

	/**
	 * {@see HgitAuthoriser::checkAuthorisation()}.
	 * @param ProjectModel $project The project that needs access to a function checked
	 * @param string $function Function string decribing the function the user wants to access in that project
	 * @param User $user Optional parameter for submitting a hgit session user object
	 * @return bool true or false if the user is allowed to access that function or not
	 */
	public static function isAllowedAction(ProjectModel $project, string $function = 'see', User $user = null): bool {
		return HgitAuthoriser::checkAuthorisation($project, $function, $user);
	}

	/**
	 * small helper function using the composer parsedown class to render markdown.
	 * @param string $markdown The given markdown file to parse
	 * @return string the parsed markdown in the form of html
	 */
	public static function renderMarkdown(string $markdown): string {
		$Parsedown = new Parsedown();

		return $Parsedown->text($markdown);
	}

	/**
	 * small helper function url encoding EVERYTHING that isn't alphanumeric to allow e.g. dots in urls.
	 * @param string $x The given url to encode
	 * @return string the urlencoded string
	 */
	public static function urlencodeall($x): string {
		$out = '';
		for ($i = 0; isset($x[$i]); $i++) {
			$c = $x[$i];
			if (!ctype_alnum($c)) {
				$c = '%'.sprintf('%02X', ord($c));
			}
			$out .= $c;
		}

		return $out;
	}
}
