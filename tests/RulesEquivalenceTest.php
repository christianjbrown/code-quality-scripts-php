<?php

declare(strict_types=1);

namespace ChristianBrown\CodeQualityScripts\Tests;

use PhpCsFixer\Config;
use PhpCsFixer\RuleSet\RuleSet;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;
use function ksort;

use const JSON_THROW_ON_ERROR;

/**
 * The risky config is composed from the safe rules plus the risky-only rules.
 * The fixtures hold the two rule arrays as they were written out in full before
 * that, so this proves composing them changed nothing about what either config does.
 *
 * @internal
 *
 * @see ../config/Safe.php
 * @see ../config/Risky.php
 */
#[CoversNothing]
final class RulesEquivalenceTest extends TestCase
{
    public function testRiskyOnlyTurnsStrictTypesOnWhereSafeLeavesThemOff(): void
    {
        $safe = include __DIR__.'/../config/Safe.php';
        $risky = include __DIR__.'/../config/Risky.php';
        self::assertInstanceOf(Config::class, $safe);
        self::assertInstanceOf(Config::class, $risky);

        self::assertFalse($safe->getRules()['declare_strict_types']);
        self::assertTrue($risky->getRules()['declare_strict_types']);
    }

    #[DataProvider('provideTheComposedRulesMatchTheOriginalFlatArrayCases')]
    public function testTheComposedRulesMatchTheOriginalFlatArray(string $config, string $fixture): void
    {
        $expected = json_decode((string) file_get_contents(__DIR__.'/data/'.$fixture), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($expected);

        $fixer = include __DIR__.'/../config/'.$config.'.php';
        self::assertInstanceOf(Config::class, $fixer);

        // Only the rules matter, not the order they are listed in.
        $actualRaw = $fixer->getRules();
        $actualResolved = (new RuleSet($actualRaw))->getRules();
        $expectedRaw = $expected['raw'];
        $expectedResolved = $expected['resolved'];
        self::assertIsArray($expectedRaw);
        self::assertIsArray($expectedResolved);
        ksort($actualRaw);
        ksort($actualResolved);
        ksort($expectedRaw);
        ksort($expectedResolved);

        self::assertSame($expectedRaw, $actualRaw);
        self::assertSame($expectedResolved, $actualResolved);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideTheComposedRulesMatchTheOriginalFlatArrayCases(): iterable
    {
        yield 'safe' => ['Safe', 'rules-safe.json'];

        yield 'risky' => ['Risky', 'rules-risky.json'];
    }
}
