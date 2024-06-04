<?php
/**
 * This file is part of the holonet project management app
 * (c) Matthias Lantsch
 *
 * @package hgit app
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */


$config['timezone'] = 'Europe/Zurich';

$config['di']['auto_wire'][\holonet\holofw\auth\Authoriser::class] = \holonet\hgit\helpers\HgitAuthoriser::class;
//$config['auth']['user_model_class'] = \holonet\hgit\models\UserModel::class;

$config['git_exe'] = '%env(GIT_BIN_PATH)%';
$config['project_root'] = '%env(PROJECT_ROOT_PATH)%';

$config['vendor_info'] = array(
	'namespace' => 'holonet',
	'author_name' => 'Matthias Lantsch',
	'author_email' => 'matthias.lantsch@bluewin.ch',
	'license' => 'http://opensource.org/licenses/gpl-license.php  GNU Public License',
	'part_of' => 'the holonet project management software'
);
