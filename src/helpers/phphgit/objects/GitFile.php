<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit\objects;

use Exception;
use holonet\hgit\helpers\phphgit\Repository;

abstract class GitFile extends GitObject {
	/**
	 * The file name relative to the repo root.
	 */
	public string $name;

	protected ?Commit $lastcommit = null;

	public function __construct(Repository $repo, string $hash, string $filename) {
		parent::__construct($repo, $hash);
		$this->name = $filename;
	}

	abstract public function getContent();

	public function lastCommit(string $refspec = ''): Commit {
		if ($this->lastcommit === null) {
			$cmd = "log -n 1 --abbrev-commit --pretty=format:'".Commit::COMMIT_FORMAT."' {$refspec} -- {$this->name}";
			$out = $this->execGit($cmd);
			if (mb_strpos($out, ':/$/:') === false) {
				throw new Exception('Failed to get last commit');
			}

			$out = explode(':/$/:', $out);
			$this->lastcommit = new Commit(
				$this->repository,
				trim($out[0], "'"), //-> hash
				$out[1], //-> author
				$out[2], //-> ago string
				$out[3], //-> msg
				$out[4], //-> timestamp
				$out[5] //-> parent hash
			);
		}

		return $this->lastcommit;
	}
}
