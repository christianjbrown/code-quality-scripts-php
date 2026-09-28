<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

use function array_filter;
use function array_map;
use function array_values;
use function sprintf;

final class ThresholdChecker implements ThresholdCheckerInterface
{
    /**
     * @param list<MetricInterface> $metrics
     *
     * @return list<string>
     */
    public function check(array $metrics, float $minimumPercent): array
    {
        $failing = array_filter(
            $metrics,
            static fn (MetricInterface $metric): bool => !$metric->meets($minimumPercent),
        );

        return array_values(array_map(
            static fn (MetricInterface $metric): string => sprintf(
                self::FAILURE_SPRINTF,
                $metric->getName(),
                $metric->getCovered(),
                $metric->getTotal(),
                $minimumPercent,
            ),
            $failing,
        ));
    }
}
