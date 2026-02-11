<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch
 *
 * @package hgit app
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\tests;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Given;
use Behat\Step\Then;
use holonet\activerecord\error\InvalidModelException;
use holonet\activerecord\error\InvalidModelStateException;
use holonet\hdev\testing\FwAppTestContext;
use holonet\hdev\testing\FwBootstrappedMinkContext;
use holonet\hgit\actions\CreateProjectDirectoryAction;
use holonet\hgit\auth\PermissionMask;
use holonet\hgit\data\cfg\ProjectDirectory;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\enum\ProjectType;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\models\UserModel;
use holonet\holofw\auth\AuthFlow;

class FeatureContext extends FwBootstrappedMinkContext {

	public const array TEST_ENV = array(
		'LOCAL_AUTH_FILE_PATH' => '%app.var_dir%/local_auth.php'
	);

	#[Given('/^I am logged with the default user$/')]
	public function actionLoginDefaultUser(): void {
		$this->scaffoldLocalAuthUser('user', 'holonet_dev_user');
		$this->actionAuthenticatedAs('user', 'holonet_dev_user');
	}

	public function scaffoldLocalAuthUser(string $username, string $password, ?string $role = null): void {
		parent::scaffoldLocalAuthUser($username, $password, $_ENV['BASE_ROLE'] ?? 'HGIT_USE');

		/** @var AuthFlow $auth */
		$auth = $this->testContext->container()->get('auth');
		$user = $auth->getAuthenticator()->passwordAuth($username, $password);
		$auth->authorise($user);

		$user = $this->testContext->repo()->get(UserModel::class, array('username' => $username));
		if ($user === null) {
			throw new ExpectationException("Failed to scaffold local auth user: user '{$username}' was not imported to database", $this->getSession());
		}
	}

	#[Given('/^the following project exists:$/')]
	#[Given('/^the following projects exist:$/')]
    public function scaffoldProjects(TableNode $table): void {
		foreach ($table as $row) {
			$permissionMasks = array('internalPermissions', 'publicPermissions');
			foreach ($permissionMasks as $permissionMask) {
				if (isset($row[$permissionMask])) {
					$permissions = array_filter(explode(',', $row[$permissionMask]));
					$row[$permissionMask] = new PermissionMask(0);
					foreach ($permissions as $perm) {
						$row[$permissionMask]->add(Permission::{$perm});
					}
				} else {
					$row[$permissionMask] = new PermissionMask(255);
				}
			}

			$row['type'] ??= ProjectType::OTHER;
			if (isset($row['maintainer'])) {
				$row['maintainer'] = $this->testContext->repo()->get(UserModel::class, array('username' => $row['maintainer']));
			} else {
				$row['maintainer'] = $this->testContext->repo()->first(UserModel::class);
			}

			$project = $this->testContext->repo()->create(ProjectModel::class, $row);
			($this->testContext->container()->instance(CreateProjectDirectoryAction::class))->execute($project);
		}
    }

	#[Then('/^there should be a main git repo for the project "([^"]+)"$/')]
    public function assertProjectGitDirectoryExists(string $project): void {
		$project = $this->testContext->repo()->mustget(ProjectModel::class, array(
			'name' => $project
		));

		$expectedPath = $this->testContext->registry()->asDto('project_root', ProjectDirectory::class)->mainRepo($project);

		if (!is_dir($expectedPath)) {
			throw new ExpectationException("Git main repo for project '{$project->name}' was not created", $this->getSession());
		}
    }
}
