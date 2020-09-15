<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit\objects;

use RuntimeException;
use holonet\hgit\helpers\phphgit\Change;
use holonet\hgit\helpers\phphgit\Repository;

class Commit extends GitObject {
	/**
	 * property for a commit pretty print format string
	 *   :/$/: => separator
	 *   %H => complete hash
	 *   %an => commit author name
	 *   %ar => commit ago string
	 *   %s => commit message string
	 *   %ct => commit timestamp.
	 * @var string COMMIT_FORMAT Pretty print format for the commit
	 */
	public const COMMIT_FORMAT = '%H:/$/:%an:/$/:%ar:/$/:%s:/$/:%ct:/$/:%P';

	public string $ago;

	/**
	 * The commit author (gitconfig.user during the creation of the commit).
	 */
	public string $author;

	public string $msg;

	public ?string $parenthash;

	public string $timestamp;

	public function __construct(Repository $repo, string $hash, string $author, string $ago, string $msg, string $ts, ?string $parent = null) {
		parent::__construct($repo, $hash);
		$this->author = $author;
		$this->ago = $ago;
		$this->msg = $msg;
		$this->timestamp = $ts;
		$this->parenthash = ($parent === '' ? null : $parent);
	}

	/**
	 * @return Change[] changes that happened in this commit
	 */
	public function details(): array {
		$ret = array();
		$cmd = "diff-tree -r --root --no-commit-id {$this->hash}";

		foreach (explode("\n", $this->execGit($cmd)) as $line) {
			if (mb_strlen($line) === 0) {
				continue;
			}

			list($oldMode, $newMode, $oldHash, $newHash, $change) = explode(' ', $line);
			list($changeType, $filename) = explode("\t", $change);
			$ret[] = new Change(
				$this->repository,
				$oldHash, //-> old object blob
				$newHash, //-> new object blob
				$changeType, //-> change type
				$filename //-> changed file
			);
		}

		return $ret;
	}

	public static function fromHash(Repository $repo, string $hash): self {
		$cmd = sprintf('log -1 -U %s --abbrev-commit --pretty=format:%s',
			$hash, self::COMMIT_FORMAT
		);

		$out = $repo->execGit($cmd);

		if (mb_strpos($out, 'fatal') === 0 || mb_strpos($out, ':/$/:') === false) {
			throw new RuntimeException("Could not find commit by hash {$hash}");
		}

		$headline = mb_strstr($out, "\n", true);
		$line = explode(':/$/:', $headline);

		return new self(
			$repo,
			trim($line[0], "'"), //-> hash
			$line[1], //-> author
			$line[2], //-> ago string
			$line[3], //-> msg
			$line[4], //-> timestamp
			$line[5] //-> parent hash
		);
	}

	public function getDiff(): string {
		if ($this->parenthash === null) {
			return $this->execGit("diff 4b825dc642cb6eb9a060e54bf8d69288fbee4904 {$this->hash}");
		}

		return $this->execGit("diff {$this->hash}~ {$this->hash}");
	}

	public function getPath(string $subpath = ''): GitFile {
		$type = $this->execGit("cat-file -t {$this->hash}:{$subpath}");
		$hash = $this->execGit("rev-parse {$this->hash}:{$subpath}");
		if ($type === 'blob') {
			return new Blob($this->repository, $hash, $subpath);
		}
		if ($type === 'tree') {
			return new Tree($this->repository, $hash, $subpath);
		}

		throw new RuntimeException("Unknown git object type '{$type}'' at {$this->hash}:{$subpath}");
	}

	public function type(): string {
		return 'commit';
	}
}
