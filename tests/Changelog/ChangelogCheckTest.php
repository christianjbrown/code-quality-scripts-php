<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Changelog;

use ChristianBrown\CodeQualityScripts\Changelog\ChangedFilesProviderInterface;
use ChristianBrown\CodeQualityScripts\Changelog\ChangedFilesUnavailableException;
use ChristianBrown\CodeQualityScripts\Changelog\ChangelogCheck;
use ChristianBrown\CodeQualityScripts\Changelog\ChangelogCheckInterface;
use ChristianBrown\CodeQualityScripts\Changelog\ChangelogRuleInterface;
use ChristianBrown\CodeQualityScripts\Console\CheckResultInterface;
use ChristianBrown\CodeQualityScripts\Console\FileReaderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ChangelogCheck::class)]
final class ChangelogCheckTest extends TestCase
{
    public function testAnUnavailableDiffIsAnError(): void
    {
        $provider = self::createStub(ChangedFilesProviderInterface::class);
        $provider->method('getChangedFiles')->willThrowException(new ChangedFilesUnavailableException('no diff'));

        $result = (new ChangelogCheck([], $provider, self::reader('# Changelog')))->check('base');

        self::assertSame(CheckResultInterface::EXIT_ERROR, $result->getExitCode());
        self::assertSame(['no diff'], $result->getMessages());
    }

    public function testARepositoryWithoutAChangelogPasses(): void
    {
        $rule = self::createMock(ChangelogRuleInterface::class);
        $rule->expects(self::never())->method('check');

        $result = (new ChangelogCheck([$rule], self::provider(['src/A.php']), self::reader(null)))->check('base');

        self::assertSame(CheckResultInterface::EXIT_OK, $result->getExitCode());
        self::assertSame([ChangelogCheckInterface::NO_CHANGELOG_MESSAGE], $result->getMessages());
    }

    public function testEveryBrokenRuleIsReported(): void
    {
        $broken = self::createStub(ChangelogRuleInterface::class);
        $broken->method('check')->willReturn('first problem');
        $fine = self::createStub(ChangelogRuleInterface::class);
        $fine->method('check')->willReturn(null);
        $alsoBroken = self::createStub(ChangelogRuleInterface::class);
        $alsoBroken->method('check')->willReturn('second problem');

        $result = (new ChangelogCheck(
            [$broken, $fine, $alsoBroken],
            self::provider([]),
            self::reader('# Changelog'),
        ))->check('base');

        self::assertSame(CheckResultInterface::EXIT_FAILED, $result->getExitCode());
        self::assertSame(['first problem', 'second problem'], $result->getMessages());
    }

    public function testEveryRuleSeesTheChangesAndTheChangelog(): void
    {
        $rule = self::createMock(ChangelogRuleInterface::class);
        $rule->expects(self::once())->method('check')->with(['src/A.php'], '# Changelog')->willReturn(null);

        $result = (new ChangelogCheck([$rule], self::provider(['src/A.php']), self::reader('# Changelog')))->check('base');

        self::assertSame(CheckResultInterface::EXIT_OK, $result->getExitCode());
        self::assertSame([ChangelogCheckInterface::IN_STEP_MESSAGE], $result->getMessages());
    }

    public function testTheBaseRefIsPassedToTheProvider(): void
    {
        $provider = $this->createMock(ChangedFilesProviderInterface::class);
        $provider->expects(self::once())->method('getChangedFiles')->with('origin/main')->willReturn([]);

        (new ChangelogCheck([], $provider, self::reader('# Changelog')))->check('origin/main');
    }

    /**
     * @param list<string> $files
     */
    private static function provider(array $files): ChangedFilesProviderInterface
    {
        $provider = self::createStub(ChangedFilesProviderInterface::class);
        $provider->method('getChangedFiles')->willReturn($files);

        return $provider;
    }

    private static function reader(?string $contents): FileReaderInterface
    {
        $reader = self::createStub(FileReaderInterface::class);
        $reader->method('read')->willReturn($contents);

        return $reader;
    }
}
