<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

interface ChangedFilesProviderInterface
{
    /**
     * @throws ChangedFilesUnavailableException when the changes cannot be worked out
     *
     * @return list<string> paths changed between the base ref and HEAD, relative to the repository root
     */
    public function getChangedFiles(string $baseRef): array;
}
