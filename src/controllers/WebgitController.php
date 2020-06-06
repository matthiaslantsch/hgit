<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\hgit\helpers\GitContext;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\phphgit\Repository;
use Symfony\Component\HttpFoundation\Response;
use holonet\hgit\helpers\ProjectDirectoryService;
use Symfony\Component\HttpFoundation\HeaderUtils;

/**
 * WebgitController exposing a web git interface.
 */
class WebgitController extends HgitControllerBase {
	/**
	 * @var ProjectDirectoryService $di_directoryService Dependency injected project directory filesystem service
	 */
	public $di_directoryService;

	/**
	 * @var GitContext $context Current git "context" (refspec path usw...)
	 */
	private $context;

	/**
	 * @var Repository $gitrepo Reference to the repository object this context is in
	 */
	private $gitrepo;

	/**
	 * @var ProjectModel $project The currently handled project instance
	 */
	private $project;

	/**
	 * facade method sorting the parameter data and initialising the git context.
	 */
	public function __before(): void {
		parent::__before();
		if ($this->request->attributes->has('projectName')) {
			/** @var ProjectModel $project */
			$project = $this->di_repo->get(ProjectModel::class, array('name' => $this->request->attributes->get('projectName')));
			if ($project === null) {
				$this->notFound("project with the name '{$this->request->attributes->get('projectName')}'");

				return;
			}

			if (!$this->accessControl($project, 'readCode')) {
				return;
			}

			$this->project = $project;

			$this->view->set('cloneUrl', "{$this->request->getSchemeAndHttpHost()}{$this::linkInternal("{$project->slugname()}/repo/"."{$project->slugname()}.git")}");

			$this->gitrepo = $this->di_directoryService->gitRepo(
				$this->di_directoryService->projectDirectory($project),
				$this->request->attributes->get('repo')
			);

			$this->view->set('repoName', basename($this->gitrepo->path));

			$this->context = new GitContext(
				$this->gitrepo,
				//default to "master" as refspec
				urldecode($this->request->attributes->get('refspec', 'master')),
				//default to an empty path
				urldecode($this->request->attributes->get('path', ''))
			);
		}
	}

	/**
	 * blob method displaying a blob for a certain refspec
	 * GET /[projectName:a]/git/blob/[refspec:]/[path:**].
	 * @param string $projectName The name of the project that is being accessed
	 */
	public function blob(string $projectName): void {
		$this->buildwebgitNavi();
		$blob = $this->gitrepo->getPathAtRef($this->context->refspec, $this->context->path);
		if ($blob === null || $blob->type() !== 'blob') {
			$this->notFound(
				"Could not find blob at pathref '{$this->context->refspec}:{$this->context->path}' in git repo for project '{$projectName}'"
			);

			return;
		}

		$this->view->set('title', "{$projectName} Webgit - {$this->context->refspec}");
		$this->view->set('page', 'blob');
		$this->view->set('blob', $blob);
	}

	/**
	 * commit method showing the details about a commit
	 * GET /[projectName:a]/git/commit/[hash:h].
	 * @param string $projectName The name of the project that is being accessed
	 * @param string $repoName Name of the repository under the project we are accessing
	 * @param string $hash The hash of the commit to be shown in detail
	 */
	public function commit(string $projectName, string $repoName, string $hash): void {
		$this->buildwebgitNavi();
		$this->view->set('commit', $this->gitrepo->commitByHash($hash));
		$this->view->set('page', 'log');
		$this->view->set('title', "{$projectName} webgit - {$hash}");
	}

	/**
	 * commitlog method listing the git log
	 * GET /git/log/[branch:].
	 * @param string $projectName The name of the project that is being accessed
	 */
	public function commitlog(string $projectName): void {
		$this->buildwebgitNavi();

		if (!isset($this->gitrepo->branches[$this->context->refspec])) {
			$this->notFound("Could not find branch '{$this->context->refspec}' in git repo for project '{$this->project->name}'");

			return;
		}

		$this->view->set('page', 'log');
		$this->view->set('title', "{$this->project->name} webgit - {$this->context->refspec} commit log");
		$this->view->set('gitlog', $this->gitrepo->branches[$this->context->refspec]->getHistory());
	}

	/**
	 * raw method sending back a raw file from the git index
	 * GET /[projectName:a]/git/raw/[refspec:]/[path:**].
	 * @param string $projectName The name of the project that is being accessed
	 */
	public function raw(string $projectName): void {
		$blob = $this->gitrepo->getPathAtRef($this->context->refspec, $this->context->path);
		if ($blob === null || $blob->type() !== 'blob') {
			$this->notFound(
				"Could not find blob at pathref '{$this->context->refspec}:{$this->context->path}' in git repo for project '{$projectName}'"
			);

			return;
		}

		$this->response = new Response($blob->getContent());
		$disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, basename($blob->name));
		$this->response->headers->set('Content-Disposition', $disposition);
	}

	/**
	 * tags method listing all tags in the repository
	 * GET /[projectName:a]/git/tags.
	 * @param string $projectName The name of the project that is being accessed
	 */
	public function tags(string $projectName): void {
		$this->buildwebgitNavi();
		$this->view->set('tags', $this->gitrepo->tags);
		$this->view->set('title', "{$projectName} webgit - tags");
		$this->view->set('page', 'tags');
	}

	/**
	 * method for the webgit tree action
	 * GET /[projectName:a]/git/tree/[refspec:]/[path:**].
	 * @param string $projectName The name of the project that is being accessed
	 */
	public function tree(string $projectName): void {
		$this->buildwebgitNavi();
		if (!empty($this->context->path) && $this->context->path[-1] !== '/') {
			$this->context->path .= '/';
		}
		$tree = $this->gitrepo->getPathAtRef($this->context->refspec, $this->context->path);
		if ($tree === null || $tree->type() !== 'tree') {
			$this->notFound(
				"Could not find tree at pathref {$this->context->refspec}:{$this->context->path} in git repo for project {$this->project->name}"
			);

			return;
		}

		$this->view->set('title', "{$this->project->name} webgit - {$this->context->refspec}");
		$this->view->set('page', 'tree');
		$this->view->set('treeList', $tree->getContent());
	}

	/**
	 * facade method building the webgit common navigation menu
	 * as well as sort out common parameters.
	 */
	private function buildwebgitNavi(): void {
		$branches = array('branches' => array(), 'features' => array(), 'bugfixes' => array());
		foreach ($this->context->gitrepo->branches as $branch) {
			if (mb_strpos($branch->name, 'feature/') === 0) {
				$branches['features'][] = $branch->name;
			} elseif (mb_strpos($branch->name, 'fix/') === 0 || mb_strpos($branch->name, 'hotfix/') === 0) {
				$branches['bugfixes'][] = $branch->name;
			} else {
				$branches['branches'][] = $branch->name;
			}
		}

		$this->view->set('branches', $branches);
		$this->view->set('project', $this->project);
		$this->view->set('linkToBranch', $this->context->branch ?? 'master');
		$this->view->set('context', $this->context);
	}
}
