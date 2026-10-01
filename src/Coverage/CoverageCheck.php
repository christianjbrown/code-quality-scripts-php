<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

use ChristianBrown\CodeQualityScripts\Console\CheckResult;
use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;
use ChristianBrown\CodeQualityScripts\Console\FileReaderInterface;
use UnexpectedValueException;

use function sprintf;

/**
 * Reads a PHPUnit text coverage report and holds every metric in it to a floor.
 */
final readonly class CoverageCheck implements CoverageCheckInterface
{
    public function __construct(
        private FileReaderInterface $fileReader,
        private SummaryParserInterface $summaryParser,
        private ThresholdCheckerInterface $thresholdChecker,
    ) {
    }

    public function check(string $reportFile, float $minimumPercent): CheckResultInterface
    {
        $report = $this->fileReader->read($reportFile);
        if (null === $report) {
            return new CheckResult(CheckResultInterface::EXIT_ERROR, [sprintf(self::UNREADABLE_SPRINTF, $reportFile)]);
        }

        try {
            $metrics = $this->summaryParser->parse($report);
        } catch (UnexpectedValueException $exception) {
            return new CheckResult(CheckResultInterface::EXIT_ERROR, [$exception->getMessage()]);
        }

        $failures = $this->thresholdChecker->check($metrics, $minimumPercent);
        if ([] !== $failures) {
            return new CheckResult(CheckResultInterface::EXIT_FAILED, $failures);
        }

        return new CheckResult(CheckResultInterface::EXIT_OK, [sprintf(self::MET_SPRINTF, $minimumPercent)]);
    }
}
