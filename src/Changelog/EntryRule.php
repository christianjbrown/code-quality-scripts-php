<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

use function array_filter;
use function in_array;
use function str_starts_with;

/**
 * A change to the package's code must come with a changelog entry.
 */
final class EntryRule implements EntryRuleInterface
{
    /**
     * @param list<string> $changedFiles
     */
    public function check(array $changedFiles, string $changelog): ?string
    {
        $sourceChanges = array_filter(
            $changedFiles,
            static fn (string $path): bool => str_starts_with($path, self::SOURCE_PREFIX),
        );
        if ([] === $sourceChanges) {
            return null;
        }
        if (in_array(self::CHANGELOG_FILE, $changedFiles, true)) {
            return null;
        }

        return self::MISSING_ENTRY_MESSAGE;
    }
}
