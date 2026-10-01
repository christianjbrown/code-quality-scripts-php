<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Changelog;

interface GitChangedFilesProviderInterface extends ChangedFilesProviderInterface
{
    public const string COMMAND_SPRINTF = 'git -C %s diff --name-only %s HEAD 2>/dev/null';
    public const string FAILURE_SPRINTF = "Could not diff against '%s'. Fetch it first, e.g. git fetch origin main.";
}
