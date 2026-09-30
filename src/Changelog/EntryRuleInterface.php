<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

interface EntryRuleInterface extends ChangelogRuleInterface
{
    public const string MISSING_ENTRY_MESSAGE = 'src/ changed but CHANGELOG.md did not. Add a line under "## [Unreleased]" saying what changed for people using the package.';
    public const string SOURCE_PREFIX = 'src/';
}
