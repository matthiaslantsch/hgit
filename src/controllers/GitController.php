<?php
/**
 * This file is part of the holonet project management software
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
use holonet\hgit\auth\HgitAuthoriser;
use holonet\hgit\data\cfg\ProjectDirectory;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\TokenModel;
use holonet\hgit\models\UserModel;
use holonet\hgit\services\GitService;
use holonet\hgit\utils\GitResponse;
use holonet\holofw\Application;
use holonet\holofw\Context;
use holonet\holofw\Controller;
use holonet\holofw\Request;
use holonet\holofw\view\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * GitController is supposed to answer to requests of the git client.
 */
class GitController extends Controller {
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
		#[ConfigItem('project_root')] protected ProjectDirectory $projectDirectory,
		protected HgitAuthoriser $authoriser,
	) {
		parent::__construct($request, $method, $app, $database, $context, $registry, $view, $repo, $logger);
	}

	#[Route('/repo/{slug}/{repository}/{path<.+>?}', name: 'webgit_repo')]
	public function repo(ProjectModel $project, string $repository, ?string $path = null): void {
		$repo = $this->gitService->accessRepository($this->projectDirectory->repo($project, $repository));
		$user = null;

		if (
			$this->request->query->get('service') === 'git-upload-pack'
			|| $path === 'git-upload-pack' && $this->request->getMethod() === 'POST'
		) {
			// read access
			$user = $this->accessControl($project, Permission::READCODE);
		} elseif (
			$this->request->query->get('service') === 'git-receive-pack'
			|| $path === 'git-receive-pack' && $this->request->getMethod() === 'POST'
		) {
			// write access
			$user = $this->accessControl($project, Permission::WRITECODE);
		} else {
			$this->redirectInternal('webgit_show', array('slug' => $project->slug, 'repository' => $repository));
		}

		$response = new GitResponse($repo, "{$repository}/{$path}");
		if ($user !== null) {
			$response->setUser($user);
		}

		$this->finish($response);
	}

	protected function accessControl(ProjectModel $project, Permission $function): ?UserModel {
		if ($project->publicPermissions->allows($function)) {
			return null;
		}

		list('username' => $username, 'password' => $suppliedToken) = $this->request->httpBasicAuth() ?? array('username' => '', 'password' => '');
		$this->logger->debug("Basic auth supplied: {$username}:{$suppliedToken}");
		if (empty($username) || empty($suppliedToken)) {
			$this->unauthorised('No credentials supplied');
		}

		$tokens = $this->repo->select(TokenModel::class, array(
			'user.username' => $username,
			'expiry[>]' => date('Y-m-d')
		));
		if (count($tokens) === 0) {
			$this->logger->debug('No valid token found for user');
			$this->notAllowed('No valid token found for user');
		}

		// $token->permissions->allows($function)
		foreach ($tokens as $token) {
			if (password_verify($suppliedToken, $token->hash)) {
				return $token->user->get();
			}
		}

		$this->logger->debug('Failed verify');
		$this->notAllowed('Valid tokens found, failed verify');
	}
}
