<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

use function preg_match;

/**
 * The changelog keeps an Unreleased section for work not yet in a release.
 */
final class UnreleasedSectionRule implements UnreleasedSectionRuleInterface
{
    /**
     * @param list<string> $changedFiles
     */
    public function check(array $changedFiles, string $changelog): ?string
    {
        if (1 === preg_match(self::UNRELEASED_PATTERN, $changelog)) {
            return null;
        }

        return self::MISSING_SECTION_MESSAGE;
    }
}
