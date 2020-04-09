<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use RuntimeException;
use holonet\common\collection\Registry;
use holonet\hgit\helpers\phphgit\Repository;
use holonet\common\error\BadEnvironmentException;

/**
 * GitService wraps around the git binary to offer an object oriented approach to a git repo.
 */
class GitService {
	/**
	 * @var Registry $di_registry Global config values registry
	 */
	public $di_registry;

	/**
	 * @var string $gitExe Path to the git executable (validated in init())
	 */
	public $gitExe;

	/**
	 * @param string $repoPath The absolute path for the repository
	 * @return Repository git repository object for the given path
	 */
	public function accessRepository(string $repoPath): Repository {
		return new Repository($this, $repoPath);
	}

	/**
	 * method used to clone a git repository into a working dir
	 * and then returning a Repository object on that new working copy
	 * if no target directory is given, a directory in sys_temp_dir is created.
	 * @param string $url The url of the git repository to clone
	 * @param string $path The path for the cloned repository
	 * @return Repository object of the new working copy
	 */
	public function clone($url, $path = null): Repository {
		if ($path === null) {
			$path = sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($url);
		}

		$error = $this->execGit("clone {$url} {$path}");

		try {
			return new Repository($this, $path);
		} catch (RuntimeException $e) {
			throw new RuntimeException("Error cloning repository: {$error}", (int)($e->getCode()), $e);
		}
	}

	/**
	 * wrapper method enclosing the calls to shell_exec
	 * in order to access the git commands.
	 * @param string $cmd The git subcommand to execute
	 * @param string $path The path to execute the command in
	 * @param bool $ignoreFailure Flag allowing to just return anyway command failure
	 * @throws RuntimeException if the command failed and the ignoreFailure flag is false
	 * @return string with the output of the git command
	 */
	public function execGit(string $cmd, string $path = null, bool $ignoreFailure = false): string {
		$out = array();
		$returnVal = 0;
		$cmd = sprintf('"%s" %s %s 2>&1',
			$this->gitExe,
			($path === null ? '' : '-C '.escapeshellarg($path)),
			$cmd
		);

		exec($cmd, $out, $returnVal);

		$ret = implode("\n", $out);

		if ($returnVal !== 0) {
			if ($ignoreFailure) {
				return $ret;
			}

			throw new RuntimeException("Error running git command '{$cmd}'({$returnVal}):\n {$ret}", $returnVal);
		}

		return trim($ret);
	}

	/**
	 * @throws BadEnvironmentException if the git binary was not configured / is not valid
	 */
	public function init(): void {
		//if it was not configured, assume it's in the PATH and test anyway
		$this->gitExe = $this->di_registry->get('gitExe', 'git');

		try {
			$this->execGit('--version');
		} catch (RuntimeException $e) {
			throw new BadEnvironmentException(
				'Missing or invalid git executable configuration (define config key \'gitExe\' or add git to PATH)', (int)($e->getCode()), $e
			);
		}
	}
}
