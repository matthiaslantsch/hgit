<?php
/**
* This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * class file for the GitResponse class
 *
 * @package hgit project management software
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@gmail.com>
 */

namespace holonet\hgit\helpers;

use RuntimeException;
use holonet\hgit\models\ProjectModel;
use holonet\hgit\helpers\HgitUser;
use holonet\http\HttpResponse;
use holonet\http\HttpRequest;
use holonet\common as co;

/**
 * GitResponse class used to communicate with a git client using
 * the http-backend git command
 *
 * @author  matthias.lantsch
 * @package holonet\hgit\helpers
 */
class GitResponse extends HttpResponse {

  	/**
  	 * property containing an environment setup for the git process
  	 *
  	 * @access private
  	 * @var	   array $processEnvironment Array holding environment variables for the git process
  	 */
  	 private $processEnvironment;

  	/**
  	 * property containing an instance of a git repository wrapper object
	 * around the project's git repository
  	 *
  	 * @access private
  	 * @var	   Repository $gitRepo The git repository wrapper object
  	 */
  	 private $gitRepo;

	/**
	 * Constructor method to create a GitResponse
	 * will execute the command based on the request
	 *
	 * @access public
	 * @param  ProjectModel $project The project to access the git repo for
	 * @param  string $path The subpath of the file in the repo that is being accessed
	 * @return void
	 */
	public function __construct(ProjectModel $project, string $path) {
		parent::__construct();
		$this->gitRepo = $project->projectDirectory()->gitRepo();
		$this->processEnvironment = array(
			'GIT_PROJECT_ROOT' => rtrim(dirname($this->gitRepo->path), DIRECTORY_SEPARATOR), //parent directory of the git repo path
			'GIT_HTTP_EXPORT_ALL' => '1',
			'PATH_INFO' => "/{$path}"
		);
	}

	/**
	 * set a user that requested the git response
	 * sets the user environment variables for the git process
	 *
	 * @access public
	 * @param  HgitUser $user The session user sending the request
	 * @return void
	 */
	public function setUser(HgitUser $user) {
		$this->processEnvironment["GIT_COMMITTER_NAME"] = $user->name;
		if($user->email !== null) $this->processEnvironment["GIT_COMMITTER_EMAIL"] = $user->email;
		$this->processEnvironment["REMOTE_USER"] = $user->username;
	}

	/**
	 * Prepare our response in respect to the Request
	 * set additional process environment variables based on the request
	 *
	 * @access public
	 * @param  HttpRequest $request The request this response is answering
	 * @return void
	 */
	public function prepare(HttpRequest $request) {
		parent::prepare($request);
		$environmentValues = array("REMOTE_ADDR", "CONTENT_TYPE", "QUERY_STRING", "REQUEST_METHOD", "HTTP_ACCEPT", "CONTENT_LENGTH");
		foreach ($environmentValues as $pval) {
			if($request->server->has($pval)) {
				$this->processEnvironment[$pval] = $request->server->get($pval);
			}
		}

		$output = $this->gitRepo->httpBackend($this->processEnvironment, $request->body());

		if(empty($output)) {
			throw new RuntimeException('The git script returned empty output', 100);
		}

		$response = preg_split("/\R\R/", $output, 2, PREG_SPLIT_NO_EMPTY);
		$headers = $response[0];
		$this->body = isset($response[1]) ? $response[1] : "";

		foreach(preg_split("/\R/", $headers) as $header) {
			if(strpos($header, "Content-Type") !== false) {
				$this->setMimeType(str_replace("Content-Type: ", "", $header));
			} elseif(strpos($header, "Status") !== false) {
				preg_match("/Status: (\d+).*/", $header, $match);
				if(isset($match[1])) {
					$this->setStatus($match[1]);
				}
			} else {
				$header = explode(": ", $header);
				$this->header($header[0], $header[1]);
			}
		}
	}

}
