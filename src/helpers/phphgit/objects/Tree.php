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

/**
 * The Tree class represents a git file tree at a certain commit.
 */
class Tree extends GitFile {
	/**
	 * @var GitFile[] $content array of git objects contained inside the file (tree listing)
	 */
	protected $content;

	/**
	 * getter method loading in the tree content and returning it
	 * uses local property as cache.
	 * @return GitFile[] recursive tree with git objects and git trees
	 */
	public function getContent(): array {
		if ($this->content === null) {
			$output = $this->execGit("ls-tree -l --full-tree {$this->hash}:");
			$this->content = array();
			//<mode> SP <type> SP <object> SP <object size> TAB <file>
			$regex = "/\\d+ (\\w{4}) ([^ ]+) ([^\t])+\t([^\n]+)\n?/";

			//get all matches:
			preg_match_all($regex, $output, $mtchs);
			if (isset($mtchs[0])) {
				$numberOfMatches = count($mtchs[0]);
				for ($i = 0; $i < $numberOfMatches; $i++) {
					if ($mtchs[1][$i] === 'tree') {
						$this->content[] = new self(
							$this->repository,
							$mtchs[2][$i], //object hash
							"{$this->name}{$mtchs[4][$i]}" //filename
						);
					} elseif ($mtchs[1][$i] === 'blob') {
						$this->content[] = new Blob(
							$this->repository,
							$mtchs[2][$i], //object hash
							"{$this->name}{$mtchs[4][$i]}", //filename
							$mtchs[3][$i] //file size
						);
					} else {
						throw new RuntimeException("Unknown git-ls-tree object type {$mtchs[1][$i]}");
					}
				}
			}
		}

		return $this->content;
	}

	/**
	 * @return string "tree" to identify this as a tree object
	 */
	public function type(): string {
		return 'tree';
	}
}
