<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Console;

use function array_map;
use function fwrite;
use function implode;

final readonly class StreamResultReporter implements ResultReporterInterface
{
    /**
     * @param resource $output where a passing result is printed
     * @param resource $errors where any other result is printed
     */
    public function __construct(private mixed $output, private mixed $errors)
    {
    }

    public function report(CheckResultInterface $result): int
    {
        $stream = CheckResultInterface::EXIT_OK === $result->getExitCode() ? $this->output : $this->errors;
        fwrite($stream, implode('', array_map(
            static fn (string $message): string => $message."\n",
            $result->getMessages(),
        )));

        return $result->getExitCode();
    }
}
