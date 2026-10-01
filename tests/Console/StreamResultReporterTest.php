<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Console;

use ChristianBrown\CodeQualityScripts\Console\CheckResult;
use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;
use ChristianBrown\CodeQualityScripts\Console\StreamResultReporter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function fopen;
use function rewind;
use function stream_get_contents;

/**
 * @internal
 */
#[CoversClass(StreamResultReporter::class)]
final class StreamResultReporterTest extends TestCase
{
    public function testAFailingResultIsPrintedLineByLineToTheErrorStream(): void
    {
        [$output, $errors] = [self::stream(), self::stream()];

        $code = (new StreamResultReporter($output, $errors))
            ->report(new CheckResult(CheckResultInterface::EXIT_FAILED, ['a', 'b']));

        self::assertSame(CheckResultInterface::EXIT_FAILED, $code);
        self::assertSame('', self::contents($output));
        self::assertSame("a\nb\n", self::contents($errors));
    }

    public function testAnErrorResultGoesToTheErrorStream(): void
    {
        [$output, $errors] = [self::stream(), self::stream()];

        $code = (new StreamResultReporter($output, $errors))
            ->report(new CheckResult(CheckResultInterface::EXIT_ERROR, ['broken']));

        self::assertSame(CheckResultInterface::EXIT_ERROR, $code);
        self::assertSame("broken\n", self::contents($errors));
    }

    public function testAPassingResultIsPrintedToTheOutputStream(): void
    {
        [$output, $errors] = [self::stream(), self::stream()];

        $code = (new StreamResultReporter($output, $errors))
            ->report(new CheckResult(CheckResultInterface::EXIT_OK, ['fine']));

        self::assertSame(CheckResultInterface::EXIT_OK, $code);
        self::assertSame("fine\n", self::contents($output));
        self::assertSame('', self::contents($errors));
    }

    /**
     * @param resource $stream
     */
    private static function contents(mixed $stream): string
    {
        rewind($stream);

        return (string) stream_get_contents($stream);
    }

    /**
     * @return resource
     */
    private static function stream(): mixed
    {
        $stream = fopen('php://memory', 'w+b');
        self::assertIsResource($stream);

        return $stream;
    }
}
