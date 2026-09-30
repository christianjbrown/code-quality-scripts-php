<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Changelog;

use ChristianBrown\CodeQualityScripts\Changelog\EntryRule;
use ChristianBrown\CodeQualityScripts\Changelog\EntryRuleInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(EntryRule::class)]
final class EntryRuleTest extends TestCase
{
    public function testCheckFailsWhenSourceChangesWithoutAnEntry(): void
    {
        self::assertSame(
            EntryRuleInterface::MISSING_ENTRY_MESSAGE,
            (new EntryRule())->check(['src/Foo.php', 'tests/FooTest.php'], ''),
        );
    }

    /**
     * @param list<string> $changedFiles
     */
    #[TestWith([[]])]
    #[TestWith([['README.md', 'tests/FooTest.php']])]
    #[TestWith([['src/Foo.php', 'CHANGELOG.md']])]
    public function testCheckPasses(array $changedFiles): void
    {
        self::assertNull((new EntryRule())->check($changedFiles, ''));
    }
}
