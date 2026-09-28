<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Coverage;

use ChristianBrown\CodeQualityScripts\Coverage\Metric;
use ChristianBrown\CodeQualityScripts\Coverage\MetricInterface;
use ChristianBrown\CodeQualityScripts\Coverage\SummaryParser;
use ChristianBrown\CodeQualityScripts\Coverage\SummaryParserInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

use function array_map;

/**
 * @internal
 */
#[CoversClass(SummaryParser::class)]
#[UsesClass(Metric::class)]
final class SummaryParserTest extends TestCase
{
    private const string COLOURED_REPORT = "\e[1;37;40mCode Coverage Report:        \e[0m\n"
    ."\e[1;37;40m Summary:                    \e[0m\n"
    ."\e[30;42m  Classes:  100.00% (1/1)    \e[0m\n"
    ."\e[30;42m  Methods:  100.00% (3/3)    \e[0m\n"
    ."\e[30;42m  Paths:     90.00% (9/10)   \e[0m\n"
    ."\e[30;42m  Branches: 100.00% (15/15)  \e[0m\n"
    ."\e[30;42m  Lines:    100.00% (1999/2000)\e[0m\n"
    ."\n"
    ."ChristianBrown\\Example\\Thing\n"
    ."  Methods: 100.00% ( 3/ 3)   Paths:  90.00% (  9/ 10)   Lines: 100.00% ( 20/ 20)\n";

    public function testParseReadsEverySummaryMetricAndIgnoresColour(): void
    {
        $metrics = (new SummaryParser())->parse(self::COLOURED_REPORT);

        self::assertSame(
            [
                ['Classes', 1, 1],
                ['Methods', 3, 3],
                ['Paths', 9, 10],
                ['Branches', 15, 15],
                ['Lines', 1999, 2000],
            ],
            array_map(
                static fn (MetricInterface $metric): array => [$metric->getName(), $metric->getCovered(), $metric->getTotal()],
                $metrics,
            ),
        );
    }

    public function testParseThrowsWithoutASummary(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(SummaryParserInterface::NO_SUMMARY_MESSAGE);

        (new SummaryParser())->parse('OK (3 tests, 5 assertions)');
    }
}
