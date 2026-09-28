<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function escapeshellarg;
use function exec;
use function file_put_contents;
use function implode;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * End-to-end run of the php-coverage-check wrapper, which as a script outside
 * the source paths cannot be line-covered. Checks the exit code contract:
 * 0 passes, 1 is under the floor, 2 is a missing or unreadable report.
 *
 * @internal
 *
 * @see ../src/php-coverage-check
 */
#[CoversNothing]
final class CoverageCheckSmokeTest extends TestCase
{
    private const string SCRIPT = __DIR__.'/../src/php-coverage-check';

    public function testALowerFloorCanBeGiven(): void
    {
        [$code] = self::runScript("  Lines: 95.00% (95/100)\n", '90');

        self::assertSame(0, $code);
    }

    public function testAMetricUnderTheFloorFails(): void
    {
        [$code, $output] = self::runScript("  Classes: 100.00% (2/2)\n  Lines: 100.00% (1999/2000)\n");

        self::assertSame(1, $code);
        self::assertStringContainsString('Lines coverage is 1999/2000', $output);
    }

    public function testAMissingReportFails(): void
    {
        exec(escapeshellarg(self::SCRIPT).' /nonexistent/coverage.txt 2>&1', $output, $code);

        self::assertSame(2, $code);
        self::assertStringContainsString('not found or unreadable', implode("\n", $output));
    }

    public function testAReportWithoutASummaryFails(): void
    {
        [$code, $output] = self::runScript("OK (1 test)\n");

        self::assertSame(2, $code);
        self::assertStringContainsString('No coverage summary found', $output);
    }

    public function testFullCoveragePasses(): void
    {
        [$code, $output] = self::runScript("  Classes: 100.00% (2/2)\n  Lines: 100.00% (40/40)\n");

        self::assertSame(0, $code);
        self::assertStringContainsString('meets the 100% floor', $output);
    }

    /**
     * @return array{int, string}
     */
    private static function runScript(string $report, string ...$arguments): array
    {
        $file = (string) tempnam(sys_get_temp_dir(), 'coverage');
        file_put_contents($file, $report);

        $command = escapeshellarg(self::SCRIPT).' '.escapeshellarg($file);
        foreach ($arguments as $argument) {
            $command .= ' '.escapeshellarg($argument);
        }
        exec($command.' 2>&1', $output, $code);
        unlink($file);

        return [$code, implode("\n", $output)];
    }
}
