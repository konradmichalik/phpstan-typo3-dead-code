# AGENTS.md

Guidance for coding agents working in this repository.

## Project overview

A PHPStan extension for [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector). It marks members as used that TYPO3 reaches from configuration or by convention instead of PHP calls, so the detector stops reporting them as dead.

- Package: `konradmichalik/phpstan-typo3-dead-code`, type `phpstan-extension`, license GPL-3.0-or-later
- PHP `~8.2.0 || ~8.3.0 || ~8.4.0 || ~8.5.0 || ~8.6.0`, `phpstan/phpstan` `^2.1.41`, `shipmonk/dead-code-detector` `^1.4`, `symfony/yaml`
- Conflicts with `typo3/class-alias-loader` `<1.2.1`, whose `unregister()` hides `vendor/` from the detector

## Structure

- `rules.neon`: parameter schema (`typo3DeadCode.*`) and the two provider services, registered through `extra.phpstan.includes`
- `src/Typo3EntrypointProvider.php`: reflection-based provider for configuration entry points, constructors, `inject*`/`initializeObject`, Extbase actions and Fluid access
- `src/EventApiProvider.php`: node-based provider for `EventDispatcherInterface::dispatch()` calls (`markEventApi`)
- `src/PhpConfigurationReader.php` with `src/TargetCollector.php`, `src/TypoScriptUserFuncReader.php`, `src/ServicesYamlReader.php`, `src/FluidTemplateReader.php`: read targets from configuration files and templates
- `tests/src/`: PHPUnit tests mirroring `src/` (namespace `KonradMichalik\PhpStanTypo3DeadCode\Tests\`), integration tests in `tests/src/Integration/`
- `tests/Fixtures/Project/`: a small TYPO3-like project the integration tests analyse, in the `autoload-dev` classmap
- `tests/Stubs/`: TYPO3 and PSR stubs, deliberately outside the autoloader
- `docs/`: `usage.md`, `entry-points.md` and `configuration.md`

## Development commands

```bash
composer install
composer lint          # composer normalize --dry-run, editorconfig-cli (ec), php-cs-fixer --dry-run
composer fix           # same tools, applying fixes
composer sca:php       # phpstan analyse --memory-limit=2G
composer migration     # rector process -c rector.php
```

## Testing

```bash
composer test            # phpunit without coverage
composer test:coverage   # XDEBUG_MODE=coverage phpunit, reports to .build/coverage/
```

- `DefaultOptionsTest` and `InvertedOptionsTest` extend `InProcessTestCase`, a PHPStan `RuleTestCase` with the detector's `DeadCodeRule`, so the providers run inside the measured process. Expected messages live in `Expectations`
- `DeadCodeDetectorTest` runs `vendor/bin/phpstan` on the command line for what the test container cannot reproduce: locating the configuration directory, and Extbase known only through `scanDirectories`
- A new fixture member changes the sorted messages in `Expectations`

Locally, coverage needs Xdebug. CI runs the tests through the shared reusable workflow `tests-php.yml` on PHP 8.2, 8.3, 8.4, 8.5 and 8.6, and lint plus static analysis through the shared `cgl.yml`. Both are thin wrappers, there is no inline CI logic here.

## Code style and static analysis

- PHP CS Fixer uses `konradmichalik/php-cs-fixer-preset` (`.php-cs-fixer.php`), including file headers from `composer.json`. `tests/Fixtures` and `tests/Stubs` are excluded, because fixers would change the modifiers the tests depend on
- PHPStan level max over `src/` and `tests/src/`, with the PHPUnit extension
- Rector with `withPhpSets(php82: true)` over `src/` and `tests/src/`, `StringClassNameToClassConstantRector` skipped
- `composer-require-checker.json` whitelists the `PHPStan\*` and `PhpParser\*` symbols that only exist inside `phpstan.phar`
- EditorConfig is enforced through `ec` (`.editorconfig`)
- Every PHP file declares `declare(strict_types=1);`
- If an option or entry point changes, update `docs/configuration.md` and `docs/entry-points.md`

## Git workflow

- Commit format: `<type>: <description>`
- Types: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `ci`
- No co-author trailers
- One commit per logical change
