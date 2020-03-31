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

/**
 * The GitFile class represents a git file (tree/blob).
 */
abstract class GitFile extends GitObject {
	/**
	 * @var string $name The file name relative to the repo root
	 */
	public $name;

	/**
	 * @var Commit|null $lastcommit Commit object for the commit that last changed this file
	 */
	protected $lastcommit;

	/**
	 * @param Repository $repo Reference to the opened git repository
	 * @param string $hash The hash of the file
	 * @param string $filename The name of the file (tree/blob)
	 */
	public function __construct(Repository $repo, string $hash, string $filename) {
		parent::__construct($repo, $hash);
		$this->name = $filename;
	}

	/**
	 * small function to get the content of the file.
	 */
	abstract public function getContent();

	/**
	 * @param string $refspec Reference as to where to look for the last commit
	 * @throws Exception
	 * @return Commit object when the file was last changed
	 */
	public function lastCommit(string $refspec = ''): Commit {
		if ($this->lastcommit === null) {
			$cmd = "log -n 1 --abbrev-commit --pretty=format:'".Commit::COMMIT_FORMAT."' {$refspec} -- {$this->name}";
			$out = $this->execGit($cmd);
			if (mb_strpos($out, ':/$/:') === false) {
//				dd($cmd);
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
