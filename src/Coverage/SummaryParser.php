<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

use UnexpectedValueException;

use function array_map;
use function preg_match_all;
use function preg_replace;

use const PREG_SET_ORDER;

final class SummaryParser implements SummaryParserInterface
{
    /**
     * @return non-empty-list<MetricInterface>
     */
    public function parse(string $report): array
    {
        $plain = (string) preg_replace(self::ANSI_ESCAPE_PATTERN, '', $report);
        preg_match_all(self::METRIC_PATTERN, $plain, $matches, PREG_SET_ORDER);

        $metrics = array_map(
            static fn (array $match): Metric => new Metric($match[1], (int) $match[2], (int) $match[3]),
            $matches,
        );
        if ([] === $metrics) {
            throw new UnexpectedValueException(self::NO_SUMMARY_MESSAGE);
        }

        return $metrics;
    }
}
