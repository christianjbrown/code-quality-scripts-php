<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

interface UnreleasedSectionRuleInterface extends ChangelogRuleInterface
{
    public const string MISSING_SECTION_MESSAGE = 'CHANGELOG.md has no "## [Unreleased]" heading. Keep one at the top so the next change has somewhere to go.';
    public const string UNRELEASED_PATTERN = '/^## \[Unreleased\]\s*$/m';
}
