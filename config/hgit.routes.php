<?php

use holonet\hgit\controllers\GitController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
	$routes->add('webgit_repo', '/{projectName}/repo/{repoName}/{path<.+>?}')
		->defaults(array(
			'_controller' => GitController::class, '_method' => 'repo',
			'_methodParams' => array('projectName', 'repoName', 'path')
		));
};
