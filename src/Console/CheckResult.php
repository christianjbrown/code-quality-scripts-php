<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Console;

final readonly class CheckResult implements CheckResultInterface
{
    /**
     * @param int          $exitCode one of the CheckResultInterface::EXIT_ constants
     * @param list<string> $messages
     */
    public function __construct(private int $exitCode, private array $messages)
    {
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @return list<string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
