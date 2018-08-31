<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * procedural file for defining view helper functions
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

 use holonet\hgit\models\ProjectModel;
 use holonet\hgit\helpers\HgitAuthoriser;

 /**
  * small helper function checking if the currently logged in user has access to this project in the way specified by the parameter
  * makes use of the integer bitmask in the database
  * will check in the following order to reduce lookups:
  *  check if the function is globally allowed (public perm mask)
  *  check if internally allowed (other perm mask)
  *  check if the user has his own permission mask
  *
  * @param  ProjectModel $project The project that needs access to a function checked
  * @param  string $function Function string decribing the function the user wants to access in that project
  * @return boolean true or false if the user is allowed to access that function or not
  */
function isAllowedAction(ProjectModel $project, string $function = "see") {
	if(isset($_SESSION["hgituser"])) {
		return HgitAuthoriser::checkAuthorisation($project, $function, $_SESSION["hgituser"]);
	} else {
		return HgitAuthoriser::checkAuthorisation($project, $function);
	}
}

/**
 * small helper function using the composer parsedown class to render markdown
 *
 * @param  string $markdown The given markdown file to parse
 * @return string the parsed markdown in the form of html
 */
function renderMarkdown($markdown) {
	require_once "Parsedown.php";
	$Parsedown = new Parsedown();
	return $Parsedown->text($markdown);
}

/**
 * small helper function url encoding EVERYTHING that isn't alphanumeric to allow e.g. dots in urls
 *
 * @param  string $x The given url to encode
 * @return string the urlencoded string
 */
function urlencodeall($x) {
    $out = '';
    for ($i = 0; isset($x[$i]); $i++) {
        $c = $x[$i];
        if (!ctype_alnum($c)) $c = '%' . sprintf('%02X', ord($c));
        $out .= $c;
    }
    return $out;
}

/**
 * small helper function using the highlight.js php port to render a code pre tag
 *
 * @param  string $code The code to be highlighted
 * @param  string $lang The language to highlight with
 * @return string html markup with hightlighted code inside <pre><code> tags
 */
function highlightCode($code, $lang = null) {
	$hl = new Highlight\Highlighter();
	if($lang !== null) {
		$r = $hl->highlight($lang, $code);
	} else {
		$r = $hl->highlightAuto($code);
	}
	return "<pre><code class=\"hljs {$r->language}\">{$r->value}</code></pre>";
}
