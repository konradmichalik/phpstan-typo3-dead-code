<div align="center">

# PHPStan TYPO3 dead code

[![Tests](https://img.shields.io/github/actions/workflow/status/konradmichalik/phpstan-typo3-dead-code/tests.yml?label=tests&logo=github)](https://github.com/konradmichalik/phpstan-typo3-dead-code/actions/workflows/tests.yml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/konradmichalik/phpstan-typo3-dead-code/php?logo=php)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)

</div>

TYPO3 entry points for [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector). The detector has no TYPO3 support, so it reports everything TYPO3 calls from configuration instead of PHP code. A route target such as `FacetsModalController::configuration` is only named in `Configuration/Backend/AjaxRoutes.php`, and the detector reports it as unused.

## 🔥 Installation

### Requirements

- PHP 8.2 to 8.6
- PHPStan 2.1.41 or newer
- [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector) 1.4, installed as a dependency
- `typo3/class-alias-loader` 1.2.1 or newer, if the project uses it. Older versions unregister the Composer class loader, so the detector no longer sees which classes come from `vendor/` and reports every override of a TYPO3 or PSR method, such as `MiddlewareInterface::process`. The package declares a conflict, `composer update typo3/class-alias-loader` resolves it

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

### PHPStan Started from a Subdirectory

If PHPStan lives in its own project, for example `Tests/CGL`, two things matter:

```yaml
# Tests/CGL/phpstan.neon

parameters:
  paths:
    - ../../Classes
    - ../../Configuration
    - ../../ext_localconf.php
```

```bash
vendor/bin/phpstan analyse --autoload-file=../../vendor/autoload.php
```

- The provider finds the configuration through the analysed `Configuration` path. Without such a path it falls back to `Configuration/` in the working directory, which is the wrong place here, and every route target and callback is reported.
- `--autoload-file` makes the detector recognise overrides of the extension's own dependencies. In a trial `bootstrapFiles` did not help, the command line option did.

With both in place the result matched a run from the project root on four extensions.

## 🧰 Entry Points

| Source | Marked as used |
|--------|----------------|
| `Configuration/**/*.php` | Route targets `Foo::class . '::bar'` and `'Vendor\Foo::bar'`, an invokable `'target' => Foo::class` (`__invoke`), and TCA callbacks such as `'itemsProcFunc' => Foo::class . '->bar'` or `'userFunc' => 'Vendor\Foo->bar'` |
| `Configuration/**/*.typoscript` | `userFunc = Vendor\Foo->bar`, comment lines excluded |
| `ext_localconf.php`, `ext_tables.php` | The same targets as in `Configuration/**/*.php`, such as `'clearCachePostProc'][] = Foo::class . '->bar'`, and DataHandler hook classes registered as `processDatamapClass`, `processCmdmapClass`, `moveRecordClass` or `checkModifyAccessList`, whose hook methods such as `processDatamap_afterDatabaseOperations` count as used |
| Any class | `__construct`, because the container instantiates it |
| Extbase controllers | Every `*Action` method, `initialize*Action` included, of a class extending `ActionController` |

```php
// Configuration/Backend/AjaxRoutes.php
'target' => FacetsModalController::class . '::configuration', // FacetsModalController::configuration counts as used
```

Class names are resolved to fully qualified names, so two classes with the same short name do not cover each other. A route that names a child class also covers a method declared in its parent class or in a trait. A PHP file that cannot be parsed is skipped.

### Not Covered

- Fluid templates, `Services.yaml` tags, `USER:` conditions and hooks other than the DataHandler ones that are registered by class name only. Methods that are only reachable from there are still reported.
- Properties of value objects that Fluid templates or JSON serialisation read. Set `shipmonkDeadCode.detect.deadProperties.neverRead` to `false` if that produces noise.
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
    configurationDirectory: %currentWorkingDirectory%/../../Configuration
    markConstructors: true
```

| Option | Default | Effect |
|--------|---------|--------|
| `configurationDirectory` | none | Directory scanned for PHP and TypoScript files. `ext_localconf.php` and `ext_tables.php` next to it are read as well. Without it, the analysed path named `Configuration` is used, or `Configuration/` in the working directory if there is none |
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
