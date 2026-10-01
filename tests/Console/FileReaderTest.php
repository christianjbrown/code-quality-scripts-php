<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Console;

use ChristianBrown\CodeQualityScripts\Console\FileReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function chmod;
use function file_put_contents;
use function function_exists;
use function posix_geteuid;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * @internal
 */
#[CoversClass(FileReader::class)]
final class FileReaderTest extends TestCase
{
    public function testAnEmptyPathReadsAsNull(): void
    {
        self::assertNull((new FileReader())->read(''));
    }

    public function testADirectoryReadsAsNull(): void
    {
        self::assertNull((new FileReader())->read(sys_get_temp_dir()));
    }

    public function testAMissingFileReadsAsNull(): void
    {
        self::assertNull((new FileReader())->read(sys_get_temp_dir().'/no-such-file-here'));
    }

    public function testAnUnreadableFileReadsAsNull(): void
    {
        if (function_exists('posix_geteuid') && 0 === posix_geteuid()) {
            self::markTestSkipped('root can read any file');
        }
        $path = self::file();
        chmod($path, 0o000);

        self::assertNull((new FileReader())->read($path));

        chmod($path, 0o600);
        unlink($path);
    }

    public function testItReadsAFile(): void
    {
        $path = self::file();
        file_put_contents($path, 'hello');

        self::assertSame('hello', (new FileReader())->read($path));

        unlink($path);
    }

    private static function file(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'reader');
        self::assertIsString($path);

        return $path;
    }
}
