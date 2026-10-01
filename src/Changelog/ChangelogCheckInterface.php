<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;

interface ChangelogCheckInterface
{
    public const string IN_STEP_MESSAGE = 'Changelog is in step with the changes.';
    public const string NO_CHANGELOG_MESSAGE = 'No CHANGELOG.md, so there is nothing to check.';

    public function check(string $baseRef): CheckResultInterface;
}
