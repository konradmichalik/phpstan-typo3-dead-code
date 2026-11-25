<div align="center">

![icon](icon.png)

# PHPStan TYPO3 dead code

[![Coverage](https://img.shields.io/coverallsCoverage/github/konradmichalik/phpstan-typo3-dead-code?logo=coveralls)](https://coveralls.io/github/konradmichalik/phpstan-typo3-dead-code)
[![CGL](https://img.shields.io/github/actions/workflow/status/konradmichalik/phpstan-typo3-dead-code/cgl.yml?label=cgl&logo=github)](https://github.com/konradmichalik/phpstan-typo3-dead-code/actions/workflows/cgl.yml)
[![Tests](https://img.shields.io/github/actions/workflow/status/konradmichalik/phpstan-typo3-dead-code/tests.yml?label=tests&logo=github)](https://github.com/konradmichalik/phpstan-typo3-dead-code/actions/workflows/tests.yml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/konradmichalik/phpstan-typo3-dead-code/php?logo=php)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)

</div>

TYPO3 entry points for [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector). The detector has no TYPO3 support, so it reports everything TYPO3 calls from configuration instead of PHP code. A route target such as `FacetsModalController::configuration` is only named in `Configuration/Backend/AjaxRoutes.php`, and the detector reports it as unused.

> [!WARNING]
> The 0.x versions are still in development. Options and behaviour may change between minor versions.

## 🔥 Installation

### Requirements

- PHP 8.2 to 8.6
- PHPStan 2.1.41 or newer
- [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector) 1.4, installed as a dependency
- `typo3/class-alias-loader` 1.2.1 or newer, if installed. Older versions hide `vendor/` from the detector, which then reports every override of a TYPO3 method as unused

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

### Result Cache

PHPStan's result cache does not know that the result depends on configuration, `Services.yaml`, TypoScript and Fluid templates. After a change to one of them alone, the next run can still report the old state. Clear the cache first:

```bash
vendor/bin/phpstan clear-result-cache
```

## 🧰 Entry Points

| Source | Marked as used |
|--------|----------------|
| `Configuration/**/*.php` | Route targets `Foo::class . '::bar'` and `'Vendor\Foo::bar'`, an invokable `'target' => Foo::class` (`__invoke`), and TCA callbacks such as `'itemsProcFunc' => Foo::class . '->bar'` or `'userFunc' => 'Vendor\Foo->bar'` |
| `Configuration/**/*.typoscript` | `userFunc = Vendor\Foo->bar`, comment lines excluded |
| `Configuration/**/*.yaml` | In the `services` section: the `method` of a tag, such as an `event.listener` with `method: onEvent`, and a `factory` written as `['@Vendor\Foo', 'create']` or `['Vendor\Foo', 'create']`. A factory service registered under a name such as `@my.factory` is resolved through its `class` |
| `ext_localconf.php`, `ext_tables.php` | The same targets as in `Configuration/**/*.php`, such as `'clearCachePostProc'][] = Foo::class . '->bar'`, and DataHandler hook classes registered as `processDatamapClass`, `processCmdmapClass`, `moveRecordClass` or `checkModifyAccessList`, whose hook methods such as `processDatamap_afterDatabaseOperations` count as used |
| Any class | `__construct`, because the container instantiates it |
| Any class | Public `inject*` methods and a public `initializeObject`, which the container calls on autowired services. Both rows follow `markConstructors` |
| Extbase controllers | Every `*Action` method, `initialize*Action` included, of a class extending `ActionController` |

```php
// Configuration/Backend/AjaxRoutes.php
'target' => FacetsModalController::class . '::configuration', // FacetsModalController::configuration counts as used
```

Class names are resolved to fully qualified names, so two classes with the same short name do not cover each other. A route that names a child class also covers a method declared in its parent class or in a trait. A PHP file that cannot be parsed is skipped.

### Not Covered

- Fluid templates, unless `markFluidAccess` is on, `calls` and `configurator` in `Services.yaml`, `Services.php`, `USER:` conditions and hooks other than the DataHandler ones that are registered by class name only. Methods that are only reachable from there are still reported.
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
    configurationDirectory: ../../Configuration
    markConstructors: true
    markEventApi: false
    markFluidAccess: false
```

| Option | Default | Effect |
|--------|---------|--------|
| `configurationDirectory` | none | Directory scanned for PHP, TypoScript and YAML files. A relative path is resolved from the directory of the `.neon` file that sets it. `ext_localconf.php` and `ext_tables.php` next to it are read as well. Without it, the analysed path named `Configuration` is used, or `Configuration/` in the working directory if there is none |
| `markConstructors` | `true` | Treats every constructor, public `inject*` method and public `initializeObject` as used. Set `false` to let the detector report them, which is often wrong without a container that creates every class |
| `markEventApi` | `false` | Treats the public methods and properties of an event as used wherever it is passed to `EventDispatcherInterface::dispatch()`, because listeners in other packages read them and write public properties that are not readonly. Dispatched through an interface or base class, the implementations and overrides of its members count too. This also hides getters nobody reads, so it is off by default. Members that only a subclass declares and objects returned by the getters are not covered |
| `markFluidAccess` | `false` | Reads every `*.html` and `*.txt` under `Resources/Private/` next to the configuration directory and treats a public property `$foo` and public methods `getFoo()`, `isFoo()` and `hasFoo()` as used when a variable path such as `{record.foo}` names `foo`. Quoted strings such as translation keys do not count. The match is by name only, because the template does not say which class `record` is. A generic name such as `value` or `date` therefore also hides that member on unrelated classes, so it is off by default |

## 🧑‍💻 Development

`composer test` runs PHPUnit, including an end to end run of PHPStan with the detector on `tests/Fixtures`.

```bash
composer test                # PHPUnit
composer test:coverage       # PHPUnit with a clover report in .build/coverage
composer lint                # composer normalize, editorconfig and PHP-CS-Fixer (dry run)
composer sca                 # PHPStan
composer migration           # Rector
composer fix
```

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
