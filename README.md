<div align="center">

# PHPStan TYPO3 dead code

[![Tests](https://img.shields.io/github/actions/workflow/status/konradmichalik/phpstan-typo3-dead-code/tests.yml?label=tests&logo=github)](https://github.com/konradmichalik/phpstan-typo3-dead-code/actions/workflows/tests.yml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/konradmichalik/phpstan-typo3-dead-code/php?logo=php)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)

</div>

TYPO3 entry points for [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector). The detector has no TYPO3 support, so it reports everything TYPO3 calls from configuration instead of PHP code. A route target such as `FacetsModalController::configuration` is only named in `Configuration/Backend/AjaxRoutes.php`, and the detector reports it as unused.

## 🔥 Installation

### Requirements

- PHP 8.2 to 8.5
- PHPStan 2.1.41 or newer
- [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector) 1.4, installed as a dependency

### Composer

[![Packagist](https://img.shields.io/packagist/v/konradmichalik/phpstan-typo3-dead-code?label=version&logo=packagist)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)
[![Packagist Downloads](https://img.shields.io/packagist/dt/konradmichalik/phpstan-typo3-dead-code?color=brightgreen)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)

```bash
composer require konradmichalik/phpstan-typo3-dead-code --dev
```

## ⚡ Usage

If you have the [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer)
package installed, there is nothing more to do. The detector and the [provider](rules.neon)
are included automatically.

### Manual Setup

```yaml
# phpstan.neon

includes:
  - %rootDir%/../../shipmonk/dead-code-detector/rules.neon
  - %rootDir%/../../konradmichalik/phpstan-typo3-dead-code/rules.neon

parameters:
  paths:
    - Classes
    - Configuration
    - ext_localconf.php
```

Add `ext_localconf.php` and `Configuration/` to `paths`. Calls from there only count when they are analysed.

## 🧰 Entry Points

| Source | Marked as used |
|--------|----------------|
| `Configuration/Backend/*Routes.php` | `Foo::class . '::bar'`, `'Vendor\Foo::bar'` and an invokable `'target' => Foo::class` (`__invoke`) |
| `Configuration/**/*.typoscript` | `userFunc = Vendor\Foo->bar` |
| Any class | `__construct`, because the container instantiates it |

```php
// Configuration/Backend/AjaxRoutes.php
'target' => FacetsModalController::class . '::configuration', // FacetsModalController::configuration counts as used
```

Class names are resolved to fully qualified names, so two classes with the same short name do not cover each other.

### Not Covered

- Fluid templates, `Services.yaml` tags and hook registrations in `ext_localconf.php`. Methods that are only reachable from there are still reported.
- Overrides of TYPO3 base classes and interfaces, such as ViewHelpers, toolbar items and event listeners. The detector's own vendor provider already handles them.
- Public API for other packages. Mark it with `@api` in the PHPDoc, the detector honours that out of the box:

```php
/**
 * @api
 */
public function configByContext(): void {}
```

## ⚙️ Configuration

```yaml
parameters:
  typo3DeadCode:
    configurationDirectory: %currentWorkingDirectory%/Configuration
    markConstructors: true
```

| Option | Default | Effect |
|--------|---------|--------|
| `configurationDirectory` | `%currentWorkingDirectory%/Configuration` | Directory scanned for route files and TypoScript |
| `markConstructors` | `true` | Treats every constructor as used. Set `false` to let the detector report unused constructors, which is often wrong without a container that creates every class |

## 🧑‍💻 Development

`composer test` runs PHPUnit, including an end to end run of PHPStan with the detector on `tests/Fixtures`.

```bash
composer test   # PHPUnit
composer lint   # composer normalize (dry run) and editorconfig check
composer fix
```

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
