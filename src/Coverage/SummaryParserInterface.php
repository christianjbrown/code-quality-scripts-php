<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Coverage;

use UnexpectedValueException;

interface SummaryParserInterface
{
    public const string ANSI_ESCAPE_PATTERN = '/\e\[[0-9;]*m/';
    public const string METRIC_PATTERN = '/^\s*(Classes|Methods|Paths|Branches|Lines):\s+(?:[0-9.]+%\s+)?\((\d+)\/(\d+)\)/m';
    public const string NO_SUMMARY_MESSAGE = 'No coverage summary found. Is this a PHPUnit --coverage-text report?';

    /**
     * Reads the summary block of a PHPUnit `--coverage-text` report. Only the
     * summary is read: per-class lines further down use a different layout.
     *
     * @throws UnexpectedValueException when the report holds no summary
     *
     * @return non-empty-list<MetricInterface>
     */
    public function parse(string $report): array;
}
