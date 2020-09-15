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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\HeaderUtils;
use holonet\hgit\services\ProjectDirectoryService;

/**
 * WebgitController exposing a web git interface.
 * @Route("/{projectName}/repo/{repoName?}")
 */
class WebgitController extends HgitControllerBase {
	public ProjectDirectoryService $di_directoryService;

	private GitContext $context;

	private Repository $gitrepo;

	private ProjectModel $project;

	/**
	 * facade method sorting the parameter data and initialising the git context.
	 */
	public function __before(): void {
		parent::__before();
		if ($this->request->attributes->has('projectName')) {
			/** @var ProjectModel $project */
			$project = $this->di_repo->get(ProjectModel::class, array('name' => $this->request->attributes->get('projectName')));
			if ($project === null) {
				throw $this->notFound("project with the name '{$this->request->attributes->get('projectName')}'");
			}

			if (!$this->accessControl($project, 'readCode')) {
				return;
			}

			$this->project = $project;

			$this->view->set('cloneUrl', "{$this->request->getSchemeAndHttpHost()}{$this::linkInternal('webgit_repo', array('projectName' => $project->slugname(), 'repoName' => "{$project->slugname()}.git"))}");

			$this->gitrepo = $this->di_directoryService->gitRepo(
				$this->di_directoryService->projectDirectory($project),
				$this->request->attributes->get('repo')
			);

			$this->view->set('repoName', basename($this->gitrepo->path));

			$this->context = new GitContext(
				$this->gitrepo,
				//default to "master" as refspec
				urldecode($this->request->attributes->get('refspec') ?? 'master'),
				//default to an empty path
				urldecode($this->request->attributes->get('path', ''))
			);
		}
	}

	/**
	 * @Route("/blob/{refspec?}/{path<.+>?}")
	 */
	public function blob(string $projectName): void {
		$this->buildwebgitNavi();
		$blob = $this->gitrepo->getPathAtRef($this->context->refspec, $this->context->path);

		if ($blob === null || $blob->type() !== 'blob') {
			throw $this->notFound("blob at pathref '{$this->context->refspec}:{$this->context->path}' in git repo for project '{$projectName}'");
		}

		$this->view->set('title', "{$projectName} Webgit - {$this->context->refspec}");
		$this->view->set('page', 'blob');
		$this->view->set('blob', $blob);
	}

	/**
	 * @Route("/commit/{refspec}")
	 */
	public function commit(string $projectName, string $repoName, string $hash): void {
		$this->buildwebgitNavi();
		$this->view->set('commit', $this->gitrepo->commitByHash($hash));
		$this->view->set('page', 'commitlog');
		$this->view->set('title', "{$projectName} webgit - {$hash}");
	}

	/**
	 * @Route("/log/{refspec}")
	 */
	public function commitlog(string $projectName): void {
		$this->buildwebgitNavi();

		if (!isset($this->gitrepo->branches[$this->context->refspec])) {
			throw $this->notFound("Could not find branch '{$this->context->refspec}' in git repo for project '{$this->project->name}'");
		}

		$this->view->set('page', 'commitlog');
		$this->view->set('title', "{$this->project->name} webgit - {$this->context->refspec} commit log");
		$this->view->set('gitlog', $this->gitrepo->branches[$this->context->refspec]->getHistory());
	}

	/**
	 * @Route("/raw/{refspec}/{path<.+>}")
	 */
	public function raw(string $projectName): void {
		$blob = $this->gitrepo->getPathAtRef($this->context->refspec, $this->context->path);
		if ($blob === null || $blob->type() !== 'blob') {
			throw $this->notFound("blob at pathref '{$this->context->refspec}:{$this->context->path}' in git repo for project '{$projectName}'");
		}

		$this->response = new Response($blob->getContent());
		$disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, basename($blob->name));
		$this->response->headers->set('Content-Disposition', $disposition);
	}

	/**
	 * @Route("/tags/{refspec?}")
	 */
	public function tags(string $projectName): void {
		$this->buildwebgitNavi();
		$this->view->set('tags', $this->gitrepo->tags);
		$this->view->set('title', "{$projectName} webgit - tags");
		$this->view->set('page', 'tags');
	}

	/**
	 * @Route("/", name="webgit_show")
	 * @Route("/tree/{refspec?}/{path<.+>?}")
	 */
	public function tree(string $projectName): void {
		$this->buildwebgitNavi();
		if (!empty($this->context->path) && $this->context->path[-1] !== '/') {
			$this->context->path .= '/';
		}
		$tree = $this->gitrepo->getPathAtRef($this->context->refspec, $this->context->path);
		if ($tree === null || $tree->type() !== 'tree') {
			throw $this->notFound("Could not find tree at pathref {$this->context->refspec}:{$this->context->path} in git repo for project {$this->project->name}");
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
