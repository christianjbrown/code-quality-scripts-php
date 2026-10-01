<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Console;

/**
 * What a check wants its script to print and exit with.
 */
interface CheckResultInterface
{
    public const int EXIT_ERROR = 2;
    public const int EXIT_FAILED = 1;
    public const int EXIT_OK = 0;

    /**
     * @return int one of the EXIT_ constants
     */
    public function getExitCode(): int;

    /**
     * @return list<string> lines to print, without line endings
     */
    public function getMessages(): array;
}
