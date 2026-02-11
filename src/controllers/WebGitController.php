<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use holonet\activerecord\Database;
use holonet\activerecord\ModelRepository;
use holonet\common\collection\Registry;
use holonet\common\di\autowire\attribute\ConfigItem;
use holonet\hgit\auth\ProjectAccess;
use holonet\hgit\data\cfg\ProjectDirectory;
use holonet\hgit\data\GitRepositoryContext;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\services\GitService;
use holonet\holofw\Application;
use holonet\holofw\Context;
use holonet\holofw\Controller;
use holonet\holofw\Request;
use holonet\holofw\view\View;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[ProjectAccess(Permission::READCODE)]
#[Route('/git/{slug}/{repository}')]
class WebGitController extends Controller {
	public function __construct(
		Request $request,
		string $method,
		Application $app,
		Database $database,
		Context $context,
		Registry $registry,
		View $view,
		ModelRepository $repo,
		LoggerInterface $logger,
		protected GitService $gitService,
		#[ConfigItem('project_root')] protected ProjectDirectory $projectDirectory
	) {
		parent::__construct($request, $method, $app, $database, $context, $registry, $view, $repo, $logger);
	}

	#[Route('/blob/{ref?}/{path<.+>?}')]
	public function blob(ProjectModel $model, string $repository, ?string $ref = null, ?string $path = null): void {
		$context = $this->collectGitRepoContext($model, $repository, $ref, $path);

		$blob = $context->repository->getPathAtRef($context->ref, $context->path);
		if ($blob === null || $blob->type() !== 'blob') {
			$this->notFound("blob at pathref '{$context->ref}:{$context->path}' in git repo '{$repository}' for project '{$model->name}'");
		}

		$this->shareGlobal('title', "{$model->name} webgit - {$context->ref}");
		$this->share('page', 'blob');
		$this->share('blob', $blob);

		$this->render('webgit/blob');
	}

	#[Route('/commit/{ref}')]
	public function commit(ProjectModel $model, string $repository, string $ref): void {
		$context = $this->collectGitRepoContext($model, $repository, $ref);

		try {
			$this->share('commit', $context->repository->commitByHash($ref));
		} catch (RuntimeException) {
			$this->notFound("commit by hash '{$ref}' in git repo '{$repository}' for project '{$model->name}'");
		}
		$this->share('page', 'commit');
		$this->shareGlobal('title', "{$model->name} webgit - {$ref}");

		$this->render('webgit/commit');
	}

	#[Route('/log/{ref}')]
	public function log(ProjectModel $model, string $repository, string $ref): void {
		$context = $this->collectGitRepoContext($model, $repository, $ref);

		if (!isset($context->repository->branches[$ref])) {
			$this->notFound("branch '{$ref}' in git repo '{$repository}' for project '{$model->name}'");
		}

		$this->share('page', 'log');
		$this->shareGlobal('title', "{$model->name} webgit - {$ref} commit log");
		$this->share('gitlog', $context->repository->branches[$ref]->getHistory());

		$this->render('webgit/log');
	}

	#[Route('/raw/{ref}/{path<.+>}')]
	public function raw(ProjectModel $model, string $repository, string $ref, string $path): void {
		$context = $this->collectGitRepoContext($model, $repository, $ref, $path);

		$blob = $context->repository->getPathAtRef($ref, $path);
		if ($blob === null || $blob->type() !== 'blob') {
			$this->notFound("blob at pathref '{$context->ref}:{$context->path}' in git repo '{$repository}' for project '{$model->name}'");
		}

		$response = new Response($blob->getContent());
		$disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, basename($blob->name));
		$response->headers->set('Content-Disposition', $disposition);

		$this->finish($response);
	}

	#[Route('/tags/{tag?}')]
	public function tags(ProjectModel $model, string $repository, ?string $tag = null): void {
		$context = $this->collectGitRepoContext($model, $repository, $tag);

		$this->share('tags', $context->repository->tags);
		$this->shareGlobal('title', "{$model->name} webgit - tags");
		$this->share('page', 'tags');

		$this->render('webgit/tags');
	}

	#[Route('/', name: 'webgit_show', methods: array('GET'))]
	#[Route('/tree/{ref?}/{path<.+>?}', methods: array('GET'))]
	public function tree(ProjectModel $model, string $repository, ?string $ref = null, ?string $path = null): void {
		$context = $this->collectGitRepoContext($model, $repository, $ref, $path);

		if (!empty($this->context->path) && $this->context->path[-1] !== '/') {
			$this->context->path .= '/';
		}
		$tree = $context->repository->getPathAtRef($context->ref, $context->path);
		if ($tree === null || $tree->type() !== 'tree') {
			$this->notFound("tree at pathref '{$context->ref}:{$context->path}' in git repo '{$repository}' for project '{$model->name}'");
		}

		$this->shareGlobal('title', "{$model->name} webgit - {$context->ref}");
		$this->share('page', 'tree');
		$this->share('treeList', $tree->getContent());

		$this->render('webgit/tree');
	}

	/**
	 * facade method building the webgit common navigation menu
	 * as well as sort out common parameters.
	 */
	private function buildWebGitNavi(GitRepositoryContext $repositoryContext): void {
		$branches = array('branches' => array(), 'features' => array(), 'bugfixes' => array());
		foreach ($repositoryContext->repository->branches as $branch) {
			if (mb_strpos($branch->name, 'feature/') === 0) {
				$branches['features'][] = $branch->name;
			} elseif (mb_strpos($branch->name, 'fix/') === 0 || mb_strpos($branch->name, 'hotfix/') === 0) {
				$branches['bugfixes'][] = $branch->name;
			} else {
				$branches['branches'][] = $branch->name;
			}
		}

		$this->share('branches', $branches);
		$this->share('linkToBranch', $repositoryContext->branch ?? 'master');
		$this->share('context', $repositoryContext);
	}

	private function collectGitRepoContext(ProjectModel $project, string $repository, ?string $ref = null, ?string $path = null): GitRepositoryContext {
		$this->share('cloneUrl', "{$this->request->getSchemeAndHttpHost()}{$this::linkInternal('webgit_repo', array('slug' => $project->slug, 'repository' => "{$project->slug}.git"))}");

		try {
			$repository = $this->gitService->accessRepository($this->projectDirectory->repo($project, $repository));
			$this->share('repoName', basename($repository->path));

			$context = new GitRepositoryContext(
				$project,
				$repository,
				// default to "master" as ref
				urldecode($ref ?? 'master'),
				// default to an empty path
				urldecode($path ?? '')
			);

			$this->buildwebgitNavi($context);

			return $context;
		} catch (RuntimeException) {
			$this->notFound("Repository '{$repository}' for project {$project->name}");
		}
	}
}
