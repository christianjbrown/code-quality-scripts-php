# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.1] - 2026-10-01

### Changed

- The archive Composer installs no longer contains the tests, CI and editor configuration, `CLAUDE.md` or other development-only files, only the library itself, its README, CHANGELOG and LICENSE.

## [1.2.0] - 2026-10-01

### Added

- `ChangelogCheck`, `CoverageCheck` and the shared `CheckResult`, `StreamResultReporter` and
  `FileReader` classes behind `php-changelog-check` and `php-coverage-check`, with
  `GitChangedFilesProvider` for the changed-file list. The scripts' arguments, output and exit
  codes are unchanged.

### Changed

- `config/Risky.php` is now built from `config/rules/SafeRules.php` plus
  `config/rules/RiskyOnlyRules.php`, so each rule lives in one place. The rules both configs apply
  are identical to before, and the files you include are still `config/Risky.php` and
  `config/Safe.php`.
- This package's own PHPStan run now includes `config/phpstan.neon`, so the static private method
  rule is applied to its code as it is for consumers.

## [1.1.1] - 2026-10-01

### Fixed

- `php-coverage-check` now reads a coverage summary for code with nothing to measure. PHPUnit
  prints such metrics as `(0/0)` with no percentage, which the parser did not recognise, so the
  check failed with "No coverage summary found" on packages that hold only interfaces and empty
  classes.

## [1.1.0] - 2026-09-30

### Added

- `php-changelog-check`, which fails a pull request that changes `src/` without adding a
  `CHANGELOG.md` entry, or whose `CHANGELOG.md` has lost its `## [Unreleased]` heading.
  Repositories without a `CHANGELOG.md` are skipped.

## [1.0.0] - 2026-09-28

First stable release.

### Added

- The `ChristianBrown` PHP_CodeSniffer standard (PHP_CodeSniffer 4 with slevomat sniffs) and the
  risky and safe PHP CS Fixer rule sets.
- The `php-cs`, `php-cs-diff`, `php-cs-fix` and `php-cs-fix-diff` wrappers.
- A PHPStan configuration consumers can include, with the `RequireStaticPrivateMethodRule` rule.
- `php-coverage-check`, which fails the build when any metric in a PHPUnit coverage summary
  (classes, methods, paths, branches, lines) falls below a floor, 100% by default.

[Unreleased]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.2.1...HEAD
[1.2.1]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/christianjbrown/code-quality-scripts-php/releases/tag/v1.0.0
