<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

/**
 * @var array<string, array<string, mixed>|bool> $rules
*/
$rules = include __DIR__.'/rules/SafeRules.php';

$config = new Config('safe');
$config->setRiskyAllowed(false);
$config->setUsingCache(false);
$config->setRules($rules);
$config->setParallelConfig(ParallelConfigFactory::detect());

return $config;
