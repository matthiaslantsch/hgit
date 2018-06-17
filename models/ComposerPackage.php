<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Extension class used to represent a composer library project
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\models;

use holonet\common as co;
use holonet\holofw\FWController;

/**
 * ComposerPackage class to wrap around the "project" database table
 * represents a composer project
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\models
 */
class ComposerPackage extends ProjectModel {

	/**
	 * make sure this is still wrapping the "project" table
	 *
	 * @access protected
	 * @var    string $tableName The name of the table in the dbms
	 */
	protected static $tableName = "project";

	/**
	 * property containing the id of the project type "php library"
	 * populated by the first call to the static method "projectTypeId()"
	 *
	 * @access private
	 * @var	   integer $projectTypeId The project type id of a "composer" type project
	 */
	private static $projectTypeId;

	/**
	 * method used to determine the project type id
	 * uses static property as "cache"
	 *
	 * @access public
	 * @return intger with the id of the composer library project type
	 */
	public static function projectTypeId(array $options = array()) {
		if(static::$projectTypeId === null) {
			static::$projectTypeId = ProjectTypeModel::get(array("name" => "php library", "SELECT" => "idProjectType"));
		}
		return static::$projectTypeId;
	}

	/**
	 * helper function generating a project json for the composer package.json file
	 *
	 * @access public
	 * @return array with the requested data
	 */
	public function versions() {
		$repo = $this->projectDirectory()->gitRepo();
		$devComposerJson = $repo->getPathAtRef("develop", "composer.json");
		$devComposerJson = json_decode($devComposerJson, true);
		$devComposerJson["version"] = "dev-develop";
		$devComposerJson["source"] = array(
			"type" => "git",
			"url" => "https://{$_SERVER["HTTP_HOST"]}".FWController::linkInternal("{$this->slugname()}/repo/{$this->slugname()}.git"),
			"reference" => "origin/develop"
		);
		return array("dev-develop" => $devComposerJson);
	}

	/**
	 * query SELECT method for a normal query with conditions
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return model object or null if not found
	 */
	public static function select(array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::select($options);
	}

	/**
	 * query SELECT EXISTS for checking if an entry exists
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return boolean true or false if exists or not
	 */
	public static function exists(array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::exists($options);
	}

	/**
	 * query SELECT COUNT() for counting the number of entries
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return integer with the count of entries
	 */
	public static function count(array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::count($options);
	}

	/**
	 * query SELECT MAX(column) for selecting the max amount of a column
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  string $column Column name for various sql functions that need one
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return mixed the maximum value of the given column
	 */
	public static function max($column, array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::max($options);
	}

	/**
	 * query SELECT MIN(column) for selecting the min amount of a column
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  string $column Column name for various sql functions that need one
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return mixed the minimum value of the given column
	 */
	public static function min($column, array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::min($options);
	}

	/**
	 * query SELECT AVG(column) for selecting the average amount of a column
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  string $column Column name for various sql functions that need one
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return mixed the average value of the given column
	 */
	public static function avg($column, array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::avg($options);
	}

	/**
	 * query SELECT SUM(column) for selecting the sum amount of a column
	 * overwritten method to add project type to the options
	 *
	 * @access public
	 * @param  string $column Column name for various sql functions that need one
	 * @param  array $options Array with condition column to value mappings/options for the query
	 * @return mixed the sum of all values of the given column
	 */
	public static function sum($column, array $options = array()) {
		$options["idProjectType"] = static::projectTypeId();
		return parent::sum($options);
	}

}
