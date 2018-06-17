<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class filefor the ProjectDirectory class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use RuntimeException;
use holonet\common as co;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\phphgit\Repository;
use holonet\hgit\helpers\phphgit\PHPHGit as HGit;

 /**
 * The ProjectDirectory class is used to wrap around a standardized project directory on the file system
  *
  * @author  matthias.lantsch
  * @package holonet\hgit\helpers
  */
class ProjectDirectory {

	/**
	 * property containing the base path of the directory
	 *
	 * @access public
	 * @var    string $basepath String with the base path of the project directory
	 */
	public $basepath;

	/**
	 * property containing the project model
	 *
	 * @access public
	 * @var    Projectmodel $project ProjectModel instance of the project this directory belongs to
	 */
	public $project;

	/**
	 * constructor method taking the ProjectModel instance as an argument
	 *
	 * @access public
	 * @param  ProjectModel $project Project model instance that the directory will belong to
	 * @return void
	 */
	public function __construct(ProjectModel $project) {
		$root = static::projectRootDir();
		$this->basepath = $root.DIRECTORY_SEPARATOR.$project->slugname();
		if(!file_exists($this->basepath)) {
			throw new RuntimeException("Project directory '{$this->basepath}' does not exist", 10);
		}

		$this->project = $project;
	}

	/**
	 * static function creating a new project directory according to standards
	 *
	 * @access public
	 * @param  ProjectModel $project Project model instance that the directory will belong to
	 * @return void
	 */
	public static function create($project) {
		$root = static::projectRootDir();
		$basepath = $root.DIRECTORY_SEPARATOR.$project->slugname();
		if(file_exists($basepath)) {
			throw new RuntimeException("Project directory '{$basepath}' already exists", 10);
		}

		$tempnewdir = sys_get_temp_dir().DIRECTORY_SEPARATOR.$project->slugname()."_temp";
		if(file_exists($tempnewdir)) {
			self::rm($tempnewdir);
		}

		mkdir($tempnewdir);

		//create git repository folder
		mkdir($tempnewdir.DIRECTORY_SEPARATOR."REPO");
		$gitRepo = $tempnewdir.DIRECTORY_SEPARATOR."REPO".DIRECTORY_SEPARATOR.$project->slugname().".git";

		//create the main git repository
		$tempdir = sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($gitRepo);
		self::rm($tempdir);
		HGit::execGit("init --bare $gitRepo");

		//clone a working copy into a temporary folder
		$repo = Repository::clone($gitRepo);
		$repo->execGit("config user.name 'hgit daemon'");
		$repo->execGit("config user.email 'hgitdaemon@localhost'");
		$repo->add("version", "{$project->version()}\n");
		$repo->commit("Create version file in master branch");
		$repo->execGit("push --set-upstream origin master");

		//create wiki branch
		$repo->checkout("wiki", true);
		$repo->add("readme.md", "{$project->description}\n");
		$repo->commit("Create readme file in wiki branch");
		$repo->execGit("push --set-upstream origin wiki");
		$repo->checkout("master");

		//create developement branch
		$repo->checkout("develop", true);
		$repo->add("develop", "Basic idea is that you don't commit to this branch directly, but instead create feature/bugfix branches\n");
		$repo->commit("Create develop index file in develop branch");
		$repo->execGit("push --set-upstream origin develop");

		//delete the temporary working directory
		self::rm($repo->path);

		//"commit" the newly created folder structure to the project root
		rename($tempnewdir, $basepath);
	}

	/**
	 * static helper function getting the project root directory and falling back
	 * to defaults if it wasn't configured
	 *
	 * @access public
	 * @return string with the project directory we are using
	 * @throws RuntimeException if the project root directory does not exist
	 */
	public static function projectRootDir() {
		$rootpth = co\Registry::get("projectRoot");
		if($rootpth === false) {
			//default to var/projects/
			$root = $rootpth = co\dirpath(co\registry("app.vardir"), "projects");
			if(!file_exists($root)) {
				mkdir($root, 0755);
			}
		}

		$root = realpath($rootpth);
		if($root === false) {
			throw new RuntimeException("Project root directory '{$rootpth}' does not exist", 10);
		}
		return $root;
	}

	/**
	 * method used to access the hgit_wrapper library and return a git repository object for this project
	 *
	 * @access public
	 * @return Repository object from phphgit library
	 */
	public function gitRepo() {
		$gitRepo = $this->basepath.DIRECTORY_SEPARATOR."REPO".DIRECTORY_SEPARATOR.$this->project->slugname().".git";
		return HGit::access($gitRepo);
	}

	/**
	 * rm function used to delete recursively
	 *
	 * @access public
	 * @param  string $path The directory to rm recursively
	 * @return false or true on error or not
	 */
	public static function rm($path) {
		if(empty($path) || !file_exists($path)) {
			return false;
		}

		foreach(array_unique(array_merge(glob($path . DIRECTORY_SEPARATOR . '*'), glob($path . DIRECTORY_SEPARATOR . '{,.}*', GLOB_BRACE))) as $f) {
			if(realpath($f) !== $path && substr($f, -1) !== '.') {
				if(is_dir($f)) {
					self::rm($f);
				} else {
					chmod($f, 755);
					unlink($f);
				}
			}

		}
		rmdir($path);
		return true;
	}

	/**
	 * magic _toString method for the transformation into a string
	 *
	 * @access public
	 * @return base path for this project directory
	 */
	public function __toString() {
		return $this->basepath;
	}

}
