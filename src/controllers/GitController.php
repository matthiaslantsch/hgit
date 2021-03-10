<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\controllers;

use RuntimeException;
use holonet\hgit\helpers\GitResponse;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\HgitAuthoriser;
use holonet\holofw\auth\flow\PromptAuthFlow;

/**
 * GitController is supposed to answer to requests of the git client.
 */
class GitController extends HgitControllerBase {
	public PromptAuthFlow $di_basicauth;

	public function repo(string $projectName, string $repoName, ?string $path = null): void {
		/** @var ProjectModel $project */
		$project = $this->di_repo->get(ProjectModel::class, array('name' => $this->request->attributes->get('projectName')));
		if ($project === null) {
			throw $this->notFound("project with the name '{$this->request->attributes->get('projectName')}'");
		}

		$repository = $this->di_directoryService->gitRepo(
			$this->di_directoryService->projectDirectory($project),
			$repoName
		);

		if (
			$this->request->query->get('service') === 'git-upload-pack'
			|| $path === 'git-upload-pack' && $this->request->getMethod() === 'POST'
		) {
			//read access
			if (!$this->accessControl($project, 'readCode')) {
				return;
			}
		} elseif (
			$this->request->query->get('service') === 'git-receive-pack'
			|| $path === 'git-receive-pack' && $this->request->getMethod() === 'POST'
		) {
			//write access
			if (!$this->accessControl($project, 'writeCode')) {
				return;
			}
		} else {
			$this->redirectInternal('webgit_show', array('projectName' => $project->slugname(), 'repoName' => $repoName));

			return;
			//not sure what requests are sent from clients
			//throw new RuntimeException((mb_strstr($path, '/')."Unknown type of git request '{$path}'{$this->request->__toString()}'"));
		}

		$this->response = new GitResponse($repository, "{$repoName}/{$path}");
		if (isset($this->session) && $this->session->has('user')) {
			$this->response->setUser($this->session->get('user'));
		}
	}

	/** WebgitController
	 * small helper method checking the authorisation on a project
	 * will try to authenticate the user.
	 * @param ProjectModel $project The project that needs access to a function checked
	 * @param string $function Function string describing the function the user wants to access in that project
	 * @return bool true on allowed or false
	 */
	protected function accessControl(ProjectModel $project, string $function): bool {
		if (!HgitAuthoriser::checkAuthorisation($project, $function)) {
			//anonymous access failed, auth the user
			//check if the user is allowed to access hgit as a "user" first
			$response = $this->di_basicauth->authenticate($this->request);
			if ($response !== null) {
				$this->response = $response;

				return false;
			}
			$sessionUser = $this->session()->get('user');
			$this->di_basicauth->authorise($sessionUser);

			if (!HgitAuthoriser::checkAuthorisation($project, $function, $sessionUser)) {
				throw $this->notAllowed("Function '{$function}' for project '{$project->name}' was denied");
			}
		}

		return true;
	}
}
