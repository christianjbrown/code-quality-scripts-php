<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Coverage;

use ChristianBrown\CodeQualityScripts\Coverage\Metric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Metric::class)]
final class MetricTest extends TestCase
{
    public function testGettersReturnTheConstructorValues(): void
    {
        $metric = new Metric('Lines', 9, 10);

        self::assertSame('Lines', $metric->getName());
        self::assertSame(9, $metric->getCovered());
        self::assertSame(10, $metric->getTotal());
    }

    #[TestWith([10, 10, 100.0, true])]
    #[TestWith([1999, 2000, 100.0, false])]
    #[TestWith([9, 10, 90.0, true])]
    #[TestWith([8, 10, 90.0, false])]
    #[TestWith([0, 0, 100.0, true])]
    public function testMeetsComparesTheRawCounts(int $covered, int $total, float $minimumPercent, bool $expected): void
    {
        self::assertSame($expected, (new Metric('Lines', $covered, $total))->meets($minimumPercent));
    }
}
