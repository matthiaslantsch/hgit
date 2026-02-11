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
use holonet\activerecord\Model;
use holonet\activerecord\ModelFinder;
use holonet\activerecord\ModelRepository;
use holonet\common\collection\Registry;
use holonet\hgit\actions\CreateProjectDirectoryAction;
use holonet\hgit\auth\ProjectAccess;
use holonet\hgit\data\form\ProjectCreateForm;
use holonet\hgit\data\form\ProjectUpdateForm;
use holonet\hgit\models\enum\Permission;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\services\GitService;
use holonet\holofw\Application;
use holonet\holofw\attributes\auth\Authorised;
use holonet\holofw\base\rest\HasDetailPage;
use holonet\holofw\base\rest\RESTController;
use holonet\holofw\base\rest\RESTFormBehaviour;
use holonet\holofw\Context;
use holonet\holofw\Request;
use holonet\holofw\view\View;
use Psr\Log\LoggerInterface;

class ProjectsController extends RESTController {
	use HasDetailPage;
	use RESTFormBehaviour;

	public const string URL_IDENTIFIER = 'slug';

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
		ModelFinder $modelFinder,
		protected GitService $gitService
	) {
		parent::__construct($request, $method, $app, $database, $context, $registry, $view, $repo, $logger, $modelFinder);
	}

	#[Authorised(permission: 'HGIT_USE')]
	public function new(): void {
		$this->shareFormState();
		$this->restNewForm();
	}

	#[Authorised(permission: 'HGIT_USE')]
	public function create(): void {
		$this->database->transaction();
		$model = $this->restCreate($this->consumeRequestBody());
		$this->app->container->instance(CreateProjectDirectoryAction::class)->execute($model);
		$this->database->commit();

		$this->flash('success', 'Project created successfully');
		$this->modelCreated($model);
	}

	#[ProjectAccess]
	public function show(ProjectModel $model): void {
		$this->restShow($model);
	}

	#[ProjectAccess(Permission::ADMIN)]
	public function edit(ProjectModel $model): void {
		$this->shareFormState();
		$this->restEditForm($model);
	}

	#[ProjectAccess(Permission::ADMIN)]
	public function update(ProjectModel $model): void {
		$requestBody = $this->consumeRequestBody();

		$this->restUpdate($model, $requestBody);
		$this->flash('success', 'Project updated successfully');
		$this->redirectInternal($model);
	}

	protected function restShow(Model $model, ?string $title = null): void {
		$this->share('statistics', $this->gitService->getStatistics($model));
		parent::restShow($model, $title);
	}

	protected function consumeRequestBody(?string $dtoClass = null, ?string $formKey = null, array $extraData = array()): array|object {
		if ($this->method === 'create') {
			$extraData['maintainer'] = $this->getAppUser();
		}

		return $this->requestBody($this->method === 'create' ? ProjectCreateForm::class : ProjectUpdateForm::class, $formKey, $extraData);
	}
}
