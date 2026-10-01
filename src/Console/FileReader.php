<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Console;

use function file_get_contents;
use function is_file;

final class FileReader implements FileReaderInterface
{
    public function read(string $path): ?string
    {
        if (!is_file($path)) {
            return null;
        }
        $contents = @file_get_contents($path);

        return false === $contents ? null : $contents;
    }
}
