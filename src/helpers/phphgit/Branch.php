<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

use holonet\hgit\helpers\phphgit\objects\Commit;

/**
 * The Branch class logically represents a branch on a git repository.
 */
class Branch extends objects\GitObject {
	/**
	 * @var string $name The branch name in the repo
	 */
	public $name;

	/**
	 * @param Repository $repo Object of an open git repository
	 * @param string $hash Hash representing this object
	 * @param string $name The name of this branch on the repository
	 */
	public function __construct(Repository $repo, string $hash, string $name) {
		//the "hash" in a branch sense is not an object hash, but the object
		//hash of the HEAD commit
		parent::__construct($repo, $hash);
		$this->name = $name;
	}

	/**
	 * @return Commit object for the current HEAD commit
	 */
	public function getHEAD(): Commit {
		return objects\Commit::fromHash($this->repository, $this->hash);
	}

	/**
	 * method collecting history information about either the
	 * whole repo on a branch or a subpath (can even be a file).
	 * @param string $path Subpath inside the repo (empty for branch log)
	 * @return Commit[] history log consisting of Commit objects
	 */
	public function getHistory(string $path = ''): array {
		$ret = array();
		$cmd = "log --abbrev-commit --pretty=format:'".objects\Commit::COMMIT_FORMAT."' {$this->name} --";

		foreach (explode("\n", $this->execGit($cmd)) as $line) {
			if (mb_strpos($line, ':/$/:') !== false) {
				$line = explode(':/$/:', $line);
				$ret[] = new objects\Commit(
					$this->repository,
					trim($line[0], "'"), //-> hash
					$line[1], //-> author
					$line[2], //-> ago string
					$line[3], //-> msg
					$line[4], //-> timestamp
					$line[5] //-> parent hash
				);
			}
		}

		return $ret;
	}

	/**
	 * @return string "branch" to identify this as a branch object
	 */
	public function type(): string {
		return 'branch';
	}
}
