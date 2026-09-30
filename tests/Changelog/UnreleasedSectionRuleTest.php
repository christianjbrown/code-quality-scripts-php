<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests\Changelog;

use ChristianBrown\CodeQualityScripts\Changelog\UnreleasedSectionRule;
use ChristianBrown\CodeQualityScripts\Changelog\UnreleasedSectionRuleInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(UnreleasedSectionRule::class)]
final class UnreleasedSectionRuleTest extends TestCase
{
    #[TestWith([''])]
    #[TestWith(["# Changelog\n\n## [1.0.0] - 2026-09-28\n"])]
    #[TestWith(["Mentions [Unreleased] in prose only\n"])]
    public function testCheckFailsWithoutAnUnreleasedHeading(string $changelog): void
    {
        self::assertSame(
            UnreleasedSectionRuleInterface::MISSING_SECTION_MESSAGE,
            (new UnreleasedSectionRule())->check([], $changelog),
        );
    }

    #[TestWith(["# Changelog\n\n## [Unreleased]\n\n## [1.0.0] - 2026-09-28\n"])]
    #[TestWith(["## [Unreleased]  \n"])]
    public function testCheckPassesWithAnUnreleasedHeading(string $changelog): void
    {
        self::assertNull((new UnreleasedSectionRule())->check([], $changelog));
    }
}
