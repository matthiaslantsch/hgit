<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit;

use holonet\holofw\FWApplication;
use holonet\hgit\services\GitService;
use holonet\holofw\auth\flow\PromptAuthFlow;
use holonet\hgit\services\ProjectDirectoryService;

class Application extends FWApplication {
	public const APP_NAME = 'hgit';

	public function __construct() {
		parent::__construct(dirname(__DIR__));
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap(): void {
		parent::bootstrap();

		//init our git command line interface service
		$this->container->set('gitservice', GitService::class);

		//filesystem project directory service
		$this->container->set('directoryService', ProjectDirectoryService::class);

		//second lazily loaded authentication flow, just for git requests (prompting)
		$config = $this->registry->get('auth', array());
		$this->container->set('basicauth', PromptAuthFlow::class, $config);
	}
}
