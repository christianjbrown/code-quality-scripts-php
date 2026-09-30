<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

/**
 * One requirement a pull request's changelog must meet.
 */
interface ChangelogRuleInterface
{
    public const string CHANGELOG_FILE = 'CHANGELOG.md';

    /**
     * @param list<string> $changedFiles paths changed by the pull request, relative to the repository root
     * @param string       $changelog    the contents of CHANGELOG.md after the change, empty when it does not exist
     *
     * @return null|string why the rule is broken, or null when it holds
     */
    public function check(array $changedFiles, string $changelog): ?string;
}
