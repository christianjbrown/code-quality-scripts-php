<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Coverage;

use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;
use ChristianBrown\CodeQualityScripts\Console\FileReaderInterface;
use ChristianBrown\CodeQualityScripts\Coverage\CoverageCheck;
use ChristianBrown\CodeQualityScripts\Coverage\CoverageCheckInterface;
use ChristianBrown\CodeQualityScripts\Coverage\MetricInterface;
use ChristianBrown\CodeQualityScripts\Coverage\SummaryParserInterface;
use ChristianBrown\CodeQualityScripts\Coverage\ThresholdCheckerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

use function sprintf;

/**
 * @internal
 */
#[CoversClass(CoverageCheck::class)]
final class CoverageCheckTest extends TestCase
{
    public function testAnUnreadableReportIsAnError(): void
    {
        $reader = self::createStub(FileReaderInterface::class);
        $reader->method('read')->willReturn(null);

        $result = (new CoverageCheck($reader, self::parser(), self::checker([])))->check('report.txt', 100.0);

        self::assertSame(CheckResultInterface::EXIT_ERROR, $result->getExitCode());
        self::assertSame([sprintf(CoverageCheckInterface::UNREADABLE_SPRINTF, 'report.txt')], $result->getMessages());
    }

    public function testAReportWithoutASummaryIsAnError(): void
    {
        $parser = self::createStub(SummaryParserInterface::class);
        $parser->method('parse')->willThrowException(new UnexpectedValueException('no summary'));

        $result = (new CoverageCheck(self::reader('text'), $parser, self::checker([])))->check('report.txt', 100.0);

        self::assertSame(CheckResultInterface::EXIT_ERROR, $result->getExitCode());
        self::assertSame(['no summary'], $result->getMessages());
    }

    public function testMetricsMeetingTheFloorPass(): void
    {
        $result = (new CoverageCheck(self::reader('text'), self::parser(), self::checker([])))
            ->check('report.txt', 100.0);

        self::assertSame(CheckResultInterface::EXIT_OK, $result->getExitCode());
        self::assertSame([sprintf(CoverageCheckInterface::MET_SPRINTF, 100.0)], $result->getMessages());
        self::assertSame(['Coverage meets the 100% floor on every metric.'], $result->getMessages());
    }

    public function testMetricsUnderTheFloorFail(): void
    {
        $result = (new CoverageCheck(self::reader('text'), self::parser(), self::checker(['Lines coverage is 1/2'])))
            ->check('report.txt', 100.0);

        self::assertSame(CheckResultInterface::EXIT_FAILED, $result->getExitCode());
        self::assertSame(['Lines coverage is 1/2'], $result->getMessages());
    }

    public function testTheReportAndFloorReachTheCollaborators(): void
    {
        $metric = self::createStub(MetricInterface::class);
        $reader = self::createMock(FileReaderInterface::class);
        $reader->expects(self::once())->method('read')->with('report.txt')->willReturn('the report');
        $parser = self::createMock(SummaryParserInterface::class);
        $parser->expects(self::once())->method('parse')->with('the report')->willReturn([$metric]);
        $checker = self::createMock(ThresholdCheckerInterface::class);
        $checker->expects(self::once())->method('check')->with([$metric], 87.5)->willReturn([]);

        (new CoverageCheck($reader, $parser, $checker))->check('report.txt', 87.5);
    }

    /**
     * @param list<string> $failures
     */
    private static function checker(array $failures): ThresholdCheckerInterface
    {
        $checker = self::createStub(ThresholdCheckerInterface::class);
        $checker->method('check')->willReturn($failures);

        return $checker;
    }

    private static function parser(): SummaryParserInterface
    {
        $parser = self::createStub(SummaryParserInterface::class);
        $parser->method('parse')->willReturn([self::createStub(MetricInterface::class)]);

        return $parser;
    }

    private static function reader(string $contents): FileReaderInterface
    {
        $reader = self::createStub(FileReaderInterface::class);
        $reader->method('read')->willReturn($contents);

        return $reader;
    }
}
