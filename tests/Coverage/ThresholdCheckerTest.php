<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Coverage;

use ChristianBrown\CodeQualityScripts\Coverage\MetricInterface;
use ChristianBrown\CodeQualityScripts\Coverage\ThresholdChecker;
use ChristianBrown\CodeQualityScripts\Coverage\ThresholdCheckerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * @internal
 */
#[CoversClass(ThresholdChecker::class)]
final class ThresholdCheckerTest extends TestCase
{
    public function testCheckPassesWhenEveryMetricMeetsTheFloor(): void
    {
        $passing = self::createStub(MetricInterface::class);
        $passing->method('meets')->willReturn(true);

        self::assertSame([], (new ThresholdChecker())->check([$passing], 100.0));
    }

    public function testCheckPassesWithNoMetrics(): void
    {
        self::assertSame([], (new ThresholdChecker())->check([], 100.0));
    }

    public function testCheckReportsOnlyTheMetricsUnderTheFloor(): void
    {
        $passing = self::createStub(MetricInterface::class);
        $passing->method('meets')->willReturn(true);

        $failing = self::createStub(MetricInterface::class);
        $failing->method('meets')->willReturn(false);
        $failing->method('getName')->willReturn('Paths');
        $failing->method('getCovered')->willReturn(9);
        $failing->method('getTotal')->willReturn(10);

        self::assertSame(
            [sprintf(ThresholdCheckerInterface::FAILURE_SPRINTF, 'Paths', 9, 10, 100.0)],
            (new ThresholdChecker())->check([$passing, $failing], 100.0),
        );
    }
}
