<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers;

use RuntimeException;
use holonet\holofw\session\User;
use holonet\hgit\helpers\phphgit\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * GitResponse class used to communicate with a git client using
 * the http-backend git command.
 */
class GitResponse extends Response {
	/**
	 * @var Repository $gitRepo The git repository wrapper object
	 */
	private $gitRepo;

	/**
	 * @var array $processEnvironment Array holding environment variables for the git process
	 */
	private $processEnvironment;

	/**
	 * Constructor method to create a GitResponse
	 * will execute the command based on the request.
	 * @param Repository $gitRepo The git repository wrapper object
	 * @param string $path The subpath of the file in the repo that is being accessed
	 */
	public function __construct(Repository $gitRepo, string $path) {
		parent::__construct();
		$this->gitRepo = $gitRepo;
		$this->processEnvironment = array(
			'GIT_PROJECT_ROOT' => rtrim(dirname($this->gitRepo->path), DIRECTORY_SEPARATOR), //parent directory of the git repo path
			'GIT_HTTP_EXPORT_ALL' => '1',
			'PATH_INFO' => "/{$path}"
		);
	}

	/**
	 * Prepare our response in respect to the Request
	 * set additional process environment variables based on the request.
	 * {@inheritdoc}
	 */
	public function prepare(Request $request): self {
		parent::prepare($request);
		$environmentValues = array('REMOTE_ADDR', 'CONTENT_TYPE', 'QUERY_STRING', 'REQUEST_METHOD', 'HTTP_ACCEPT', 'CONTENT_LENGTH');
		foreach ($environmentValues as $pval) {
			if ($request->server->has($pval)) {
				$this->processEnvironment[$pval] = $request->server->get($pval);
			}
		}

		if ($request->headers->has('CONTENT_ENCODING')) {
			$this->processEnvironment['HTTP_CONTENT_ENCODING'] = $request->headers->get('CONTENT_ENCODING');
		}

		/** @var string $requestBody */
		$requestBody = $request->getContent();
		$output = $this->gitRepo->httpBackend($this->processEnvironment, $requestBody);

		if (empty($output)) {
			throw new RuntimeException('The git script returned empty output');
		}

		$response = preg_split('/\\R\\R/', $output, 2, PREG_SPLIT_NO_EMPTY);
		$headers = $response[0];
		$this->setContent($response[1] ?? '');

		foreach (preg_split('/\\R/', $headers) as $header) {
			if (mb_strpos($header, 'Status') !== false) {
				preg_match('/Status: (\\d+).*/', $header, $match);
				if (isset($match[1])) {
					$this->setStatusCode((int)($match[1]));
				}
			} else {
				$header = explode(': ', $header);
				$this->headers->set($header[0], $header[1]);
			}
		}

		return $this;
	}

	/**
	 * set a user that requested the git response
	 * sets the user environment variables for the git process.
	 * @param User $user The session user sending the request
	 */
	public function setUser(User $user): void {
		$this->processEnvironment['GIT_COMMITTER_NAME'] = $user->name;
		if ($user->email !== null) {
			$this->processEnvironment['GIT_COMMITTER_EMAIL'] = $user->email;
		}
		$this->processEnvironment['REMOTE_USER'] = $user->username;
	}
}
