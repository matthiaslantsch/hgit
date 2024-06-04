<?php
$config = require_once 'vendor/holonet/hdev/.php-cs-fixer.dist.php';

return $config->setFinder(PhpCsFixer\Finder::create()
	->in('src')
	->exclude('views/templates')
	->notName('schema.php')
);
