<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Console;

use ChristianBrown\CodeQualityScripts\Console\CheckResult;
use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CheckResult::class)]
final class CheckResultTest extends TestCase
{
    public function testItHoldsWhatItWasGiven(): void
    {
        $result = new CheckResult(CheckResultInterface::EXIT_FAILED, ['one', 'two']);

        self::assertSame(CheckResultInterface::EXIT_FAILED, $result->getExitCode());
        self::assertSame(['one', 'two'], $result->getMessages());
    }
}
