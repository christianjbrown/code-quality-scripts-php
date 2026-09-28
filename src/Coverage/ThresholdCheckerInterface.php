<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

interface ThresholdCheckerInterface
{
    public const string FAILURE_SPRINTF = '%s coverage is %d/%d, below the %s%% floor.';

    /**
     * @param list<MetricInterface> $metrics
     *
     * @return list<string> one message per metric under the floor; empty when all pass
     */
    public function check(array $metrics, float $minimumPercent): array;
}
