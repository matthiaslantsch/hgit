<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * class file for the logic Blob class
 */

namespace holonet\hgit\helpers\phphgit\objects;

/**
 * The Commit class represents a git blob object
 * a git blob is a file at a certain point
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package holonet\hgit\helpers\phphgit\objects
 */
class Blob extends GitFile {

	/**
	 * property for the file size
	 *
	 * @access public
	 * @var    integer size | the size of the blob in bytes
	 */
	public $size;

	/**
	 * constructor method taking the name for the branch
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | the hash of the commit
	 * @param  string filename | the name of the file (blob)
	 * @param  integer size | the size of the blob
	 */
	public function __construct($repo, $hash, $filename, $size = null) {
		parent::__construct($repo, $hash, $filename);

		//if not given, get the size with a seperate command
		if($size === null) {
			$size = intval($this->execGit("cat-file -s {$this->hash}"));
		}

		$this->size = $size;
	}

	/**
	 * getter method loading in the blob content and returning it
	 * uses local property as cache
	 *
	 * @access public
	 * @return binary blob string
	 */
	public function getContent() {
		if($this->content === null) {
			$this->content = $this->execGit("cat-file blob {$this->hash}");
		}

		return $this->content;
	}

	/**
	 * small convenience function returning the object type as a string
	 *
	 * @access public
	 * @return string "blob" to identify this as a blob object
	 */
	public function type() {
		return "blob";
	}

	/**
	 * return a neatly formatted string with the byte count
	 *
	 * @access public
	 * @return string size formatted with BKMGTP
	 */
	public function getFileSize() {
		$sz = 'BKMGTP';
		$factor = floor((strlen($this->size) - 1) / 3);
		return sprintf("%.2f", $this->size / pow(1024, $factor)).@$sz[$factor];
	}

	/**
	 * return the content if this object is casted ton a string
	 *
	 * @access public
	 * @return string the content of this blob file
	 */
	public function __toString() {
		return $this->getContent();
	}

}
