<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Changelog;

use ChristianBrown\CodeQualityScripts\Changelog\ChangedFilesUnavailableException;
use ChristianBrown\CodeQualityScripts\Changelog\GitChangedFilesProvider;
use ChristianBrown\CodeQualityScripts\Changelog\GitChangedFilesProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function escapeshellarg;
use function exec;
use function file_put_contents;
use function mkdir;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;

/**
 * @internal
 */
#[CoversClass(GitChangedFilesProvider::class)]
final class GitChangedFilesProviderTest extends TestCase
{
    public function testAnUnknownBaseRefIsUnavailable(): void
    {
        $this->expectException(ChangedFilesUnavailableException::class);
        $this->expectExceptionMessage(sprintf(GitChangedFilesProviderInterface::FAILURE_SPRINTF, 'no-such-ref'));

        (new GitChangedFilesProvider(self::repository()))->getChangedFiles('no-such-ref');
    }

    public function testItListsTheFilesChangedSinceTheBaseRef(): void
    {
        $repo = self::repository();
        file_put_contents($repo.'/src/B.php', "<?php\n");
        file_put_contents($repo.'/CHANGELOG.md', "changed\n");
        self::git($repo, 'add -A');
        self::git($repo, 'commit -q -m change');

        $files = (new GitChangedFilesProvider($repo))->getChangedFiles('base');

        self::assertSame(['CHANGELOG.md', 'src/B.php'], $files);
    }

    public function testNothingChangedIsAnEmptyList(): void
    {
        self::assertSame([], (new GitChangedFilesProvider(self::repository()))->getChangedFiles('base'));
    }

    private static function git(string $repo, string $arguments): void
    {
        exec(sprintf(
            'git -C %s -c user.email=t@example.com -c user.name=t -c commit.gpgsign=false -c core.hooksPath=/dev/null %s 2>&1',
            escapeshellarg($repo),
            $arguments,
        ));
    }

    private static function repository(): string
    {
        $repo = sys_get_temp_dir().'/git-provider-'.uniqid();
        mkdir($repo.'/src', 0o777, true);
        file_put_contents($repo.'/CHANGELOG.md', "# Changelog\n");
        file_put_contents($repo.'/src/A.php', "<?php\n");
        exec('git init -q '.escapeshellarg($repo));
        self::git($repo, 'add -A');
        self::git($repo, 'commit -q -m base');
        self::git($repo, 'tag base');

        return $repo;
    }
}
