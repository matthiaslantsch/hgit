<?php
/**
 * This file is part of the hgit git command line inferface library
 * (c) Matthias Lantsch
 *
 * class file for the logic Tag class
 */

namespace holonet\hgit\helpers\phphgit\objects;

/**
 * The Tag class represents a git tag
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package holonet\hgit\helpers\phphgit\objects
 */
class Tag extends GitObject {

	/**
	 * property for the tag name
	 *
	 * @access public
	 * @var    string name | the tag name
	 */
	public $name;

	/**
	 * constructor method taking the name for the tag
	 *
	 * @access public
	 * @param  Repository object | object of an open git repository
	 * @param  string hash | the hash of the tag
	 * @param  string name | the name of the tag
	 */
	public function __construct($repo, $hash, $name) {
		parent::__construct($repo, $hash);
		$this->name = $name;
	}

	/**
	 * small convenience function returning the object type as a string
	 *
	 * @access public
	 * @return string "tag" to identify this as a tag object
	 */
	public function type() {
		return "tag";
	}

}
