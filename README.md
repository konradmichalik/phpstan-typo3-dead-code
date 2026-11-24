# PHPStan TYPO3 dead code

Entry points for [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector) in TYPO3 extensions. The detector has no TYPO3 support, so it reports everything TYPO3 calls from configuration instead of PHP code.

```bash
composer require konradmichalik/phpstan-typo3-dead-code --dev
```

With [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer) the provider is active right away. Otherwise include `vendor/konradmichalik/phpstan-typo3-dead-code/rules.neon` next to the detector's `rules.neon`.

## What counts as used

| Source | Marked as used |
|--------|----------------|
| `Configuration/Backend/*Routes.php` | `Foo::class . '::bar'`, `'Vendor\Foo::bar'` and an invokable `'target' => Foo::class` (`__invoke`) |
| `Configuration/**/*.typoscript` | `userFunc = Vendor\Foo->bar` |
| Any class | `__construct`, because the container instantiates it |

Class names are resolved to fully qualified names, so two classes with the same short name do not cover each other.

## What is not covered

- Fluid templates, `Services.yaml` tags and hook registrations in `ext_localconf.php`. Methods that are only reachable from there are still reported.
- Overrides of TYPO3 base classes and interfaces (ViewHelpers, toolbar items, event listeners): the detector's own vendor provider already handles them.
- Public API for other packages: mark it with `@api` in the PHPDoc, the detector honours that out of the box.

Add `ext_localconf.php` and `Configuration/` to the PHPStan `paths`. Calls from there are only seen when they are analysed.

## Configuration

```neon
parameters:
    typo3DeadCode:
        configurationDirectory: %currentWorkingDirectory%/Configuration
        markConstructors: true
```

Set `markConstructors: false` to let the detector report unused constructors. Without a container that creates every class, such reports are often wrong.

## Development

```bash
composer test   # PHPUnit, including an end to end run of PHPStan on tests/Fixtures
composer lint   # composer normalize (dry run) and editorconfig check
composer fix
```

## License

GPL-3.0-or-later
