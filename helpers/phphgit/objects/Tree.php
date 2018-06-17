<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * class file for the logic Tree class
 */

namespace holonet\hgit\helpers\phphgit\objects;

/**
 * The Tree class represents a git file tree at a certain commit
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package holonet\hgit\helpers\phphgit\objects
 */
class Tree extends GitFile {

	/**
	 * getter method loading in the tree content and returning it
	 * uses local property as cache
	 *
	 * @access public
	 * @return array with recursive tree with git objects and git trees
	 */
	public function getContent() {
		if($this->content === null) {
			$output = $this->execGit("ls-tree -l --full-tree {$this->hash}:");
			$this->content = [];
			//<mode> SP <type> SP <object> SP <object size> TAB <file>
			$regex = "/\d+ (\w{4}) ([^ ]+) ([^\t])+\t([^\n]+)\n?/";

			//get all matches:
			preg_match_all($regex, $output, $mtchs);
			if(isset($mtchs[0])) {
				$numberOfMatches = count($mtchs[0]);
				for ($i=0; $i < $numberOfMatches; $i++) {
					if($mtchs[1][$i] === "tree") {
						$this->content[] = new Tree(
							$this->repository,
							$mtchs[2][$i], //object hash
							$mtchs[4][$i] //filename
						);
					} elseif($mtchs[1][$i] === "blob") {
						$this->content[] = new Blob(
							$this->repository,
							$mtchs[2][$i], //object hash
							$mtchs[4][$i], //filename
							$mtchs[3][$i] //file size
						);
					} else {
						throw new \Exception("Unknown git-ls-tree object type {$mtchs[1][$i]}", 100);
					}
				}
			}
		}
		return $this->content;
	}

	/**
	 * small convenience function returning the object type as a string
	 *
	 * @access public
	 * @return string "tree" to identify this as a tree object
	 */
	public function type() {
		return "tree";
	}

}
