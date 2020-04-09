<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch
 *
 * @package holonet php framework
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

use holonet\hgit\controllers\ComposerController;
use holonet\hgit\controllers\GitController;
use holonet\hgit\controllers\IndexController;
use holonet\hgit\controllers\ProjectsController;
use holonet\hgit\controllers\WebgitController;
use holonet\holofw\route\FWRouter;
use holonet\http\route\builder\NamespaceRouteBuilder;

/**
 * @var FWRouter $router
 */
$router = $this->router;

/* INDEX CONTROLLER */
$router->any(array(
	"url" => "login",
	"controller" => IndexController::class,
	"method" => "login"
));

$router->any(array(
	"url" => "logout",
	"controller" => IndexController::class,
	"method" => "logout"
));

//root page show the index for the projects
$router->index(array(
	"controller" => ProjectsController::class,
	"method" => "index"
));

/**
 * Expose the composer controller methods used to respond to composer requests
 */
$router->with("composer", function(NamespaceRouteBuilder $builder) {
	$builder->get(array(
		"url" => "packages.json",
		"controller" => ComposerController::class,
		"method" => "info"
	));
	$builder->get(array(
		"url" => "notify/[projectName:?s]",
		"controller" => ComposerController::class,
		"method" => "notify"
	));
});

/**
 * Expose some CRUD methods for a resource called "project"
 * use the "name" property instead of a normal id
 */
$router->with("projects", function(NamespaceRouteBuilder $builder) {
	 $builder->get(array(
		 "url" => "new",
		 "controller" => ProjectsController::class,
		 "method" => "new"
	 ));
	 $builder->post(array(
		 "url" => "",
		 "controller" => ProjectsController::class,
		 "method" => "create"
	 ));
});

/**
 * The rest of the CRUD methods and also some other functions
 * are behind an url that begins with the project name
 */
$router->with("[projectName:s]", function(NamespaceRouteBuilder $builder) {
	//show the overview for a project
	$builder->get(array(
		"url" => "",
		"controller" => ProjectsController::class,
		"method" => "show"
	));
	//expose the git repository to a git client via http
	$builder->any(array(
		"url" => "repo/[path:*]",
		"controller" => GitController::class,
		"method" => "repo"
	));
});

/**
 * All the webgit interface methods
 * are behind an url that begins with the project name and /git
 */
$router->with("[projectName:s]/git", function(NamespaceRouteBuilder $builder) {
	//for the root of the webgit interface, show the tree view at the root of the repo
	$builder->index(array(
		"controller" => WebgitController::class,
		"method" => "tree"
	));
	//tree view for file trees at a refspec
	$builder->get(array(
		"url" => "tree/[refspec:?]/[path:?*]",
		"controller" => WebgitController::class,
		"method" => "tree"
	));
	//commit page (for seeing a commit)
	$builder->get(array(
		"url" => "commit/[hash:h]",
		"controller" => WebgitController::class,
		"method" => "commit"
	));
	//log page (for viewing the commit log)
	$builder->get(array(
		"url" => "log/[refspec:?]",
		"controller" => WebgitController::class,
		"method" => "commitlog"
	));
	//list tags that are in the repository
	$builder->get(array(
		"url" => "tags/[refspec:?]", //keep the refspec here for switching back and forth
		"controller" => WebgitController::class,
		"method" => "tags"
	));
	/* blob view for seeing a blob */
	$builder->get(array(
		"url" => "blob/[refspec:?]/[path:?*]",
		"controller" => WebgitController::class,
		"method" => "blob"
	));
	/* raw view for downloading a blob */
	$builder->get(array(
		"url" => "raw/[refspec:?]/[path:?*]",
		"controller" => WebgitController::class,
		"method" => "raw"
	));
});
