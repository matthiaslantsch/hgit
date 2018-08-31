<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Model class for the ProjectModel model
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\common as co;
use holonet\activerecord\ModelBase;
use holonet\hgit\helpers\AccessMask;
use holonet\hgit\helpers\ProjectDirectory;

/**
 * ProjectModel class to wrap around the "project" database table
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class ProjectModel extends ModelBase {

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access public
	 * @var	   array with relationships
	 */
	public static $belongsTo = array("user", "projectType");

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access public
	 * @var	   array with relationships
	 */
	public static $hasMany = array("groupAccesses", "userAccesses");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var	   array with verification data
	 */
	public static $validate = array(
		"name" => array("presence", "length" => array("max" => 20), "uniqueness"),
		"major" => array("presence"),
		"minor" => array("presence"),
		"fix" => array("presence"),
		"otherMask" => array("presence")
	);

	/**
	 * property containing a reference to the AccessMask object that represents
	 * the internal access mask integer
	 *
	 * @access private
	 * @var	   AccessMask $othermaskObj The internal mask represented by an object
	 */
	 private $othermaskObj;

 	/**
 	 * property containing a reference to the AccessMask object that represents
 	 * the internal access mask integer
 	 *
 	 * @access private
 	 * @var	   AccessMask $anymaskObj The internal mask represented by an object
 	 */
 	 private $anymaskObj;

  	/**
  	 * property containing a reference to the project directory wrapper object
  	 *
  	 * @access private
  	 * @var	   ProjectDirectory $projectDir ProjectDirectory instance
  	 */
  	 private $projectDir;

	/**
	 * constructor method taking a assotiative array as an argument
	 * overwritten for hooking into the creation process
	 *
	 * @access public
	 * @param  array $data The assotiative data array
	 * @param  boolean $fromDb Boolean flag marking the data as coming from the database or not
	 * @return void
	 */
	public function __construct($data = array(), $fromDb = false) {
		if(isset($data["permissionPreset"])) {
			switch ($data["permissionPreset"]) {
				case "public":
					//allow anyone to read code and wiki
					$data["anyMask"] = AccessMask::READCODE | AccessMask::READWIKI;
					//allow authenticated users to write code and wiki as well as read project files
					$data["otherMask"] = AccessMask::READCODE | AccessMask::READWIKI
						| AccessMask::WRITECODE | AccessMask::WRITEFILES | AccessMask::READFILES;
					break;
				case "internal":
					//allow only internal users to write code and wiki as well as read project files
					$data["otherMask"] = AccessMask::READCODE | AccessMask::READWIKI
						| AccessMask::WRITECODE | AccessMask::WRITEFILES | AccessMask::READFILES;
					break;
				default:
					//set it to private
					$data["otherMask"] = 0;
					break;
			}

			unset($data["permissionPreset"]);
		}

		parent::__construct($data, $fromDb);
	}

	/**
	 * function returning a helper AccessMask object representing the internal
	 * mask so we can work with it more easily
	 * will be called by the activerecord base model class if the otherMask property
	 * is accessed
	 *
	 * @access public
	 * @return AccessMask object representing the internal integer
	 */
	public function getOtherMask() {
		if($this->othermaskObj === null) {
			$this->othermaskObj = new AccessMask($this->readAttribute("otherMask"));
		}
		return $this->othermaskObj;
	}

	/**
	 * function returning a helper AccessMask object representing the internal
	 * mask so we can work with it more easily
	 * will be called by the activerecord base model class if the anyMask property
	 * is accessed
	 *
	 * @access public
	 * @return AccessMask object representing the internal integer
	 */
	public function getAnyMask() {
		if($this->anymaskObj === null) {
			$this->anymaskObj = new AccessMask($this->readAttribute("anyMask"));
		}
		return $this->anymaskObj;
	}

	/**
	 * overwritten save method used to save the access mask integer if it changed
	 * also we want to create the project directory if the project is new
	 *
	 * @access public
	 * @return boolean on success or not
	 */
	public function save() {
		if($this->othermaskObj !== null && $this->readAttribute("otherMask") != $this->othermaskObj->mask) {
			//get the new integer into the property of the model
			$this->othermask = $this->othermaskObj->mask;
		}

		if($this->anymaskObj !== null && $this->readAttribute("anyMask") != $this->anymaskObj->mask) {
			//get the new integer into the property of the model
			$this->anyMask = $this->anymaskObj->mask;
		}

		if($this->valid() !== true) {
			return false;
		}

		//if it is just an edit of an existing one,  skip creating the project directory
		if(!$this->fromDb) {
			ProjectDirectory::create($this);
		}

		return parent::save();
	}

	/**
	 * method used create/return a project directory wrapper helper object
	 * will use caching using a property
	 *
	 * @access 	public
	 * @return  helpers\ProjectDirectory object
	 */
	public function projectDirectory() {
		if(!isset($this->projectDir)) {
			$this->projectDir = new ProjectDirectory($this);
		}

		return $this->projectDir;
	}

	/**
	 * method returning a fixed version string
	 *
	 * @access public
	 * @return version string
	 */
	public function version() {
		return sprintf("%s.%s.%s", $this->major, $this->minor, $this->fix);
	}

	/**
	 * method used to collect statistics about the project
	 *
	 * @access public
	 * @return assiciative array with statistics about this project
	 */
	public function getStatistics() {
		//get the size of the project directory
		$bytes = filesize($this->projectDirectory());
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		$ret = [
			"maintainer" => $this->user->nick,
			"size" => sprintf("%.2f", $bytes / pow(1024, $factor)).@$sz[$factor]
		];

		$repo = $this->projectDirectory()->gitRepo();
		return array_merge($ret, $repo->getStatistics());
	}

	/**
	 * small helper function used to "sluggify" the project name
	 * turns vendor/package into vendor-package
	 *
	 * @access public
	 * @return string with the sluggified project name
	 */
	public function slugname() {
		return str_replace("/", "-", $this->name);
	}

	/**
	 * small helper function used to save statistic information about a software download
	 *
	 * @access public
	 * @param  string $version String with the full version that was installed
	 * @return void
	 */
	public function notifyDownload(string $version) {
		file_put_contents(co\filepath($this->projectDirectory(), "downloads"), $version, FILE_APPEND);
	}

}
