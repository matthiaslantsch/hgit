<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch
 *
 * php route definition file
 *
 * @package holonet php framework
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

use holonet\holofw\FWRouter;

/* INDEX CONTROLLER */
FWRouter::any(array(
	"url" => "login",
	"controller" => "index",
	"method" => "login"
));

FWRouter::any(array(
	"url" => "logout",
	"controller" => "index",
	"method" => "logout"
));

//root page show the index for the projects
FWRouter::index(array(
	"controller" => "projects",
	"method" => "index"
));

/**
 * Expose the composer controller methods used to respond to composer requests
 */
FWRouter::with("composer", function($builder) {
	$builder->get(array(
		"url" => "packages.json",
		"controller" => "composer",
		"method" => "info"
	));
	$builder->get(array(
		"url" => "search.json",
		"controller" => "composer",
		"method" => "search"
	));
});

/**
 * Expose some CRUD methods for a resource called "project"
 * use the "name" property instead of a normal id
 */
FWRouter::with("projects", function($builder) {
	 $builder->get(array(
		 "url" => "new",
		 "controller" => "projects",
		 "method" => "new"
	 ));
	 $builder->post(array(
		 "url" => "",
		 "controller" => "projects",
		 "method" => "create"
	 ));
});

/**
 * The rest of the CRUD methods and also some other functions
 * are behind an url that begins with the project name
 */
FWRouter::with("[projectName:s]", function($builder) {
	//show the overview for a project
	$builder->get(array(
		"url" => "",
		"controller" => "projects",
		"method" => "show"
	));
	//expose the git repository to a git client via http
	$builder->any(array(
		"url" => "repo/[path:*]",
		"controller" => "webgit",
		"method" => "repo"
	));
});

/**
 * All the webgit interface methods
 * are behind an url that begins with the project name and /git
 */
FWRouter::with("[projectName:s]/git", function($builder) {
	//for the root of the webgit interface, show the tree view at the root of the repo
	$builder->index(array(
		"controller" => "webgit",
		"method" => "tree"
	));
	//tree view for file trees at a refspec
	$builder->get(array(
		"url" => "tree/[refspec:?]/[path:?*]",
		"controller" => "webgit",
		"method" => "tree"
	));
	//commit page (for seeing a commit)
	$builder->get(array(
		"url" => "commit/[hash:h]",
		"controller" => "webgit",
		"method" => "commit"
	));
	//log page (for viewing the commit log)
	$builder->get(array(
		"url" => "log/[refspec:?]",
		"controller" => "webgit",
		"method" => "commitlog"
	));
	//list tags that are in the repository
	$builder->get(array(
		"url" => "tags/[refspec:?]", //keep the refspec here for switching back and forth
		"controller" => "webgit",
		"method" => "tags"
	));
	/* blob view for seeing a blob */
	$builder->get(array(
		"url" => "blob/[refspec:?]/[path:?*]",
		"controller" => "webgit",
		"method" => "blob"
	));
	/* raw view for downloading a blob */
	$builder->get(array(
		"url" => "raw/[refspec:?]/[path:?*]",
		"controller" => "webgit",
		"method" => "raw"
	));
});
