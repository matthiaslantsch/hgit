<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the PHPHGit class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

use holonet\common as co;

/**
 * The PHPHGit class is the centerpiece of the PHPHGit
 * git protocol wrapper library for php
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\hgit\helpers\phphgit
 */
class PHPHGit {

	/**
	 * static function used to access a existing repository
	 *
	 * @access 	public
	 * @param   string the path to the repository
	 */
	public static function access($path) {
		//verify the given path
		if(!file_exists($path) || !is_dir($path)) {
			throw new \Exception("The given path {$path} cannot be found", 1);
		}
		//check git exe config
		if(co\registry("gitExe") === false) {
			throw new \Exception("The git exe path is not configured", 1);
		}

		return new Repository($path);
	}

	/**
	 * wrapper method enclosing the calls to shell_exec
	 * in order to access the git commands
	 *
	 * @access 	public
	 * @param   string cmd | the git subcommand to execute
	 * @param   string path | the path to execute the command in
	 * @return  the output of the git command
	 */
	public static function execGit($cmd, $path = null) {
		//check git exe config
		if(co\registry("gitExe") === false) {
			throw new \Exception("The git exe path is not configured", 1);
		}

		$out = array();
		$returnVal = 0;
		$cmd = sprintf("%s %s %s 2>&1",
			co\registry("gitExe"),
			($path === null ? "" : "-C ".escapeshellarg($path)),
			$cmd
		);

		exec($cmd, $out, $returnVal);

		$ret = implode($out, "\n");
		if($ret === null) {
			$ret = "";
		}

		if($returnVal != 0) {
			//so git show-ref returns 1 if it doesn't find anything, but it was still successfull
			if(strpos($cmd, "show-ref --tags ") !== false && $ret == "") {
				return 0;
			}
			throw new \RuntimeException("Error running git command '{$cmd}'({$returnVal}):\n {$ret}", $returnVal);
		}

		return trim($ret);
	}

}
