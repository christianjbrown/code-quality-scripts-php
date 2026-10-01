<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Console;

interface ResultReporterInterface
{
    /**
     * Prints the result's messages, to the success stream when it passed and to the error stream otherwise.
     *
     * @return int the exit code the script should end with
     */
    public function report(CheckResultInterface $result): int;
}
