# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/christianjbrown/code-quality-scripts-php/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/christianjbrown/code-quality-scripts-php/releases/tag/v1.0.0
