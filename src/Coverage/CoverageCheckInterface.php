<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;

interface CoverageCheckInterface
{
    public const string MET_SPRINTF = 'Coverage meets the %s%% floor on every metric.';
    public const string UNREADABLE_SPRINTF = "Coverage report '%s' not found or unreadable.";

    public function check(string $reportFile, float $minimumPercent): CheckResultInterface;
}
