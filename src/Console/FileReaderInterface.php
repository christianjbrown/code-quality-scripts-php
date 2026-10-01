<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Console;

interface FileReaderInterface
{
    /**
     * @return null|string the file's contents, or null when it does not exist or cannot be read
     */
    public function read(string $path): ?string;
}
