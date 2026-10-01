<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

use ChristianBrown\CodeQualityScripts\Console\CheckResult;
use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;
use ChristianBrown\CodeQualityScripts\Console\FileReaderInterface;

use function array_filter;
use function array_map;
use function array_values;

/**
 * Holds a pull request's changelog to every rule it is given.
 */
final readonly class ChangelogCheck implements ChangelogCheckInterface
{
    /**
     * @param iterable<ChangelogRuleInterface> $rules
     */
    public function __construct(
        private iterable $rules,
        private ChangedFilesProviderInterface $changedFilesProvider,
        private FileReaderInterface $fileReader,
    ) {
    }

    public function check(string $baseRef): CheckResultInterface
    {
        try {
            $changedFiles = $this->changedFilesProvider->getChangedFiles($baseRef);
        } catch (ChangedFilesUnavailableException $exception) {
            return new CheckResult(CheckResultInterface::EXIT_ERROR, [$exception->getMessage()]);
        }

        // Only repositories that keep a changelog are held to it.
        $changelog = $this->fileReader->read(ChangelogRuleInterface::CHANGELOG_FILE);
        if (null === $changelog) {
            return new CheckResult(CheckResultInterface::EXIT_OK, [self::NO_CHANGELOG_MESSAGE]);
        }

        $failures = array_values(array_filter(array_map(
            static fn (ChangelogRuleInterface $rule): ?string => $rule->check($changedFiles, $changelog),
            [...$this->rules],
        )));
        if ([] !== $failures) {
            return new CheckResult(CheckResultInterface::EXIT_FAILED, $failures);
        }

        return new CheckResult(CheckResultInterface::EXIT_OK, [self::IN_STEP_MESSAGE]);
    }
}
