<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

use function escapeshellarg;
use function exec;
use function sprintf;

final readonly class GitChangedFilesProvider implements GitChangedFilesProviderInterface
{
    /**
     * @param string $workingDirectory the git repository to diff
     */
    public function __construct(private string $workingDirectory)
    {
    }

    /**
     * @return list<string>
     */
    public function getChangedFiles(string $baseRef): array
    {
        $files = [];
        exec(
            sprintf(
                self::COMMAND_SPRINTF,
                escapeshellarg($this->workingDirectory),
                escapeshellarg($baseRef),
            ),
            $files,
            $status,
        );
        if (0 !== $status) {
            throw new ChangedFilesUnavailableException(sprintf(self::FAILURE_SPRINTF, $baseRef));
        }

        return $files;
    }
}
