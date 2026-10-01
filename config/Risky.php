<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

/**
 * @var array<string, array<string, mixed>|bool> $safeRules
*/
$safeRules = include __DIR__.'/rules/SafeRules.php';
/**
 * @var array<string, array<string, mixed>|bool> $riskyOnlyRules
*/
$riskyOnlyRules = include __DIR__.'/rules/RiskyOnlyRules.php';
// A rule set (a key starting with @) must be listed before the individual rules, because PHP CS Fixer
// lets whatever comes later win, and the risky sets would otherwise overwrite the safe rules' options.
$combined = array_merge($safeRules, $riskyOnlyRules);
$isSet = static fn (string $name): bool => str_starts_with($name, '@');
$rules = array_merge(
    array_filter($combined, $isSet, ARRAY_FILTER_USE_KEY),
    array_filter($combined, static fn (string $name): bool => !$isSet($name), ARRAY_FILTER_USE_KEY),
);

$config = new Config('risky');
$config->setRiskyAllowed(true);
$config->setUsingCache(false);
$config->setRules($rules);
$config->setParallelConfig(ParallelConfigFactory::detect());

return $config;
