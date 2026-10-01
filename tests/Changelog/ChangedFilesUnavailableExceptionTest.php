<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Changelog;

use ChristianBrown\CodeQualityScripts\Changelog\ChangedFilesUnavailableException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
#[CoversClass(ChangedFilesUnavailableException::class)]
final class ChangedFilesUnavailableExceptionTest extends TestCase
{
    public function testItIsARuntimeExceptionCarryingItsMessage(): void
    {
        $exception = new ChangedFilesUnavailableException('why');

        self::assertInstanceOf(RuntimeException::class, $exception);
        self::assertSame('why', $exception->getMessage());
    }
}
