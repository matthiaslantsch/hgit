<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the WebgitController
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use RuntimeException;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\UserModel;
use holonet\hgit\helpers\GitResponse;
use holonet\http\HttpResponse;

/**
 * WebgitController exposing a web git interface
 * as well as answering git requests
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\controllers
 */
class WebgitController extends HgitControllerBase {

	/**
	 * method for the webgit tree action
	 * GET /[projectName:a]/git/tree/[refspec:]/[path:**]
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @return the yields from the controller method
	 */
	public function tree(string $projectName) {
		yield from $this->buildwebgitNavi();
		if(!empty($this->path) && $this->path[-1] !== "/") {
			$this->path .= "/";
		}
		$tree = $this->gitrepo->getPathAtRef($this->refspec, $this->path);
		if($tree === false || $tree->type() !== "tree") {
			$this->notFound(
				"Could not find tree at pathref {$this->refspec}:{$this->path} in git repo for project {$this->project->name}"
			);
		}

		yield "title" => "{$this->project->name} webgit - {$this->refspec}";
		yield "page" => "tree";
		yield "treeList" => $tree->getContent();
	}

	/**
	 * blob method displaying a blob for a certain refspec
	 * GET /[projectName:a]/git/blob/[refspec:]/[path:**]
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @return controller method yields
	 */
	public function blob(string $projectName) {
		yield from $this->buildwebgitNavi();
		$blob = $this->gitrepo->getPathAtRef($this->refspec, $this->path);
		if($blob === false || $blob->type() !== "blob") {
			$this->notFound(
				"Could not find blob at pathref '{$this->refspec}:{$this->path}' in git repo for project '{$projectName}'"
			);
		}

		yield "title" => "{$projectName} Webgit - {$this->refspec}";
		yield "page" => "blob";
		yield "blob" => $blob;
	}

	/**
	 * raw method sending back a raw file from the git index
	 * GET /[projectName:a]/git/raw/[refspec:]/[path:**]
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @return controller method yields
	 */
	public function raw(string $projectName) {
		//After we have a GitContext object, we won't need this few lines anymore
		//default to "master" as refspec
		$this->refspec = $this->request->params->get("refspec", "master");
		//default to an empty path
		$this->path = urldecode($this->request->params->get("path", ""));
		$this->gitrepo = $this->project->projectDirectory()->gitRepo();

		$blob = $this->gitrepo->getPathAtRef($this->refspec, $this->path);
		if($blob === false || $blob->type() !== "blob") {
			$this->notFound(
				"Could not find blob at pathref '{$this->refspec}:{$this->path}' in git repo for project '{$projectName}'"
			);
		}

		$this->response = new HttpResponse();
		$this->response->header("Content-Disposition", "attachment; filename=\"".basename($blob->name)."\"");
		$this->response->body($blob->getContent());
	}

	/**
	 * method for the repo action
	 * ANY /[projectName:a]/repo/[path:*]
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @param  string $path The subpath of the file in the repo that is being accessed
	 * @return void
	 */
	public function repo(string $projectName, string $path) {
		if(
			$this->request->query->has("service") && $this->request->query->get("service") === 'git-upload-pack'
			|| strstr($path, "/") == "/git-upload-pack" && $this->request->method() == "POST"
		) {
			//read access
		} elseif(
			$this->request->query->has("service") && $this->request->query->get("service") === 'git-receive-pack'
			|| strstr($path, "/") == "/git-receive-pack" && $this->request->method() == "POST"
		) {
			//write access
			$this->accessControl($this->project, "writeCode");
		} else {
			//not sure what requests are sent from clients
			throw new RuntimeException("Unknown type of git request '{$this->request->__toString()}'");
		}

		$this->response = new GitResponse($this->project, $path);
		if(isset($this->session) && isset($this->session->user)) {
			$this->response->setUser($this->session->user);
		}
	}

	/**
	 * commitlog method listing the git log
	 * GET /git/log/[branch:]
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @return the yield from the controller method
	 */
	public function commitlog(string $projectName) {
		yield from $this->buildwebgitNavi();

		if(!isset($this->gitrepo->branches[$this->refspec])) {
			$this->notFound("Could not find branch '{$this->refspec}' in git repo for project '{$this->project->name}'");
		}

		yield "page" => "log";
		yield "title" => "{$this->project->name} webgit - {$this->refspec} commit log";
		yield "gitlog" => $this->gitrepo->branches[$this->refspec]->getHistory();
	}

	/**
	 * tags method listing all tags in the repository
	 * GET /[projectName:a]/git/tags
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @return the yield from the controller method
	 */
	public function tags(string $projectName) {
		yield from $this->buildwebgitNavi();
		yield "tags" => $this->gitrepo->tags;
		yield "title" => "{$projectName} webgit - tags";
		yield "page" => "tags";
	}

	/**
	 * commit method showing the details about a commit
	 * GET /[projectName:a]/git/commit/[hash:h]
	 *
	 * @access public
	 * @param  string $projectName The name of the project that is being accessed
	 * @param  string $hash The hash of the commit to be shown in detail
	 * @return the yield from the controller method
	 */
	public function commit(string $projectName, string $hash) {
		yield from $this->buildwebgitNavi();
		yield "commit" => $this->gitrepo->commitByHash($hash);
		yield "page" => "commit";
		yield "title" => "{$projectName} webgit - {$hash}";
	}

	/**
	 * facade method building the webgit common navigation menu
	 * as well as sort out common parameters
	 *
	 * @access private
	 * @return the yields that are used in the webgit controller menu template
	 */
	private function buildwebgitNavi() {
		//default to "master" as refspec
		$this->refspec = $this->request->params->get("refspec", "master");
		//default to an empty path
		$this->path = urldecode($this->request->params->get("path", ""));
		$this->gitrepo = $this->project->projectDirectory()->gitRepo();

		$branches = array("branches" => array(), "features" => array(), "bugfixes" => array(), "building" => array());
		foreach ($this->gitrepo->branches as $branch) {
			if(strpos($branch->name, 'feature_')) {
				$branches['features'][] = $branch->name;
			} elseif(strpos($branch->name, 'bugfix_')) {
				$branches['bugfixes'][] = $branch->name;
			} elseif(strpos($branch->name, 'build_')) {
				$branches['building'][] = $branch->name;
			} else {
				$branches['branches'][] = $branch->name;
			}
		}


		yield "branches" => $branches;
		yield "project" => $this->project;
		yield "refspec" => $this->refspec;
		yield "path" => $this->path;
		yield "gitRepo" => $this->gitrepo;
		if(isset($branches[$this->refspec])) {
			//it's a branch
			yield "branch" => $this->refspec;
		} else {
			yield "branch" => "master";
		}

		//append the webgit controller template first (with the webgit special navigation)
		$this->renderTemplate(static::getName().DIRECTORY_SEPARATOR.$this->method);
	}

	/**
	 * facade method sorting the parameter data
	 *
	 * @access public
	 * @return the yields from the controller method
	 */
	public function __before() {
		if($this->request->params->has("projectName")) {
			$this->request->params->set("projectName",
				str_replace("-", "/", $this->request->params->get("projectName"))
			);
			$project = ProjectModel::get(array("name" => $this->request->params->get("projectName")));
			if($project === null) {
				$this->notFound("project with the name '{$this->request->params->get("projectName")}'");
			}

			$this->accessControl($project, "readCode");
			$this->project = $project;
		}
	}

}
