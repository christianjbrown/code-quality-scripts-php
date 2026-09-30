<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function escapeshellarg;
use function exec;
use function file_put_contents;
use function implode;
use function mkdir;
use function realpath;
use function sys_get_temp_dir;
use function uniqid;

/**
 * End-to-end runs of the php-changelog-check wrapper against throwaway git
 * repositories. Checks the exit code contract: 0 passes, 1 is a changelog
 * problem, 2 is a usage or git error.
 *
 * @internal
 *
 * @see ../src/php-changelog-check
 */
#[CoversNothing]
final class ChangelogCheckSmokeTest extends TestCase
{
    private const string CHANGELOG = "# Changelog\n\n## [Unreleased]\n";
    private const string SCRIPT = __DIR__.'/../src/php-changelog-check';

    public function testAMissingBaseRefFails(): void
    {
        [$code, $output] = self::runIn(self::repository(self::CHANGELOG), '');

        self::assertSame(2, $code);
        self::assertStringContainsString('Usage', $output);
    }

    public function testAMissingUnreleasedHeadingFails(): void
    {
        $repo = self::repository("# Changelog\n");
        file_put_contents($repo.'/README.md', "readme\n");
        self::commit($repo);

        [$code, $output] = self::runIn($repo, 'base');

        self::assertSame(1, $code);
        self::assertStringContainsString('no "## [Unreleased]" heading', $output);
    }

    public function testARepositoryWithoutAChangelogIsSkipped(): void
    {
        $repo = self::repository(self::CHANGELOG);
        exec('cd '.escapeshellarg($repo).' && git rm -q CHANGELOG.md');
        file_put_contents($repo.'/src/Foo.php', "<?php\n");
        self::commit($repo);

        [$code, $output] = self::runIn($repo, 'base');

        self::assertSame(0, $code);
        self::assertStringContainsString('No CHANGELOG.md', $output);
    }

    public function testAnUnknownBaseRefFails(): void
    {
        [$code, $output] = self::runIn(self::repository(self::CHANGELOG), 'no-such-ref');

        self::assertSame(2, $code);
        self::assertStringContainsString('Could not diff', $output);
    }

    public function testASourceChangeWithAnEntryPasses(): void
    {
        $repo = self::repository(self::CHANGELOG);
        file_put_contents($repo.'/src/Foo.php', "<?php\n");
        file_put_contents($repo.'/CHANGELOG.md', self::CHANGELOG."\n- Add Foo.\n");
        self::commit($repo);

        [$code, $output] = self::runIn($repo, 'base');

        self::assertSame(0, $code);
        self::assertStringContainsString('in step', $output);
    }

    public function testASourceChangeWithoutAnEntryFails(): void
    {
        $repo = self::repository(self::CHANGELOG);
        file_put_contents($repo.'/src/Foo.php', "<?php\n");
        self::commit($repo);

        [$code, $output] = self::runIn($repo, 'base');

        self::assertSame(1, $code);
        self::assertStringContainsString('src/ changed but CHANGELOG.md did not', $output);
    }

    private static function commit(string $repo): void
    {
        exec('cd '.escapeshellarg($repo).' && git add -A && git -c user.email=t@example.com -c user.name=t -c commit.gpgsign=false -c core.hooksPath=/dev/null commit -q -m change');
    }

    private static function repository(string $changelog): string
    {
        $repo = sys_get_temp_dir().'/changelog-check-'.uniqid();
        mkdir($repo.'/src', 0o777, true);
        file_put_contents($repo.'/CHANGELOG.md', $changelog);
        file_put_contents($repo.'/src/Existing.php', "<?php\n");
        exec('cd '.escapeshellarg($repo).' && git init -q && git -c user.email=t@example.com -c user.name=t -c commit.gpgsign=false commit -q --allow-empty -m init && git add -A && git -c user.email=t@example.com -c user.name=t -c commit.gpgsign=false -c core.hooksPath=/dev/null commit -q -m base && git tag base');

        return $repo;
    }

    /**
     * @return array{int, string}
     */
    private static function runIn(string $repo, string $baseRef): array
    {
        $command = 'cd '.escapeshellarg($repo).' && '.escapeshellarg((string) realpath(self::SCRIPT));
        if ('' !== $baseRef) {
            $command .= ' '.escapeshellarg($baseRef);
        }
        exec($command.' 2>&1', $output, $code);

        return [$code, implode("\n", $output)];
    }
}
