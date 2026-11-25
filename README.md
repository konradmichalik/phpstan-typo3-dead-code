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

## ✨ Features

- [**Configuration entry points**](docs/entry-points.md): route targets, TCA callbacks, TypoScript `userFunc`, `Services.yaml` listener methods and factories, and DataHandler hooks count as used
- [**Container and Extbase conventions**](docs/entry-points.md): Extbase actions are not reported, and by default neither are constructors, `inject*` methods and `initializeObject`
- [**Optional event and Fluid support**](docs/configuration.md): keep the public API of dispatched events and the members read in Fluid templates
- **Fully qualified matching**: class names are resolved, so two classes with the same short name do not cover each other

## 🔥 Installation

### Requirements

- PHP 8.2, 8.3, 8.4, 8.5 or 8.6
- PHPStan 2.1.41 or newer
- [`shipmonk/dead-code-detector`](https://github.com/shipmonk-rnd/dead-code-detector) 1.4 (installed automatically as a dependency)
- `typo3/class-alias-loader` 1.2.1 or newer, if installed. Older versions hide `vendor/` from the detector, which then reports every override of a TYPO3 method as unused

### Composer

[![Packagist](https://img.shields.io/packagist/v/konradmichalik/phpstan-typo3-dead-code?label=version&logo=packagist)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)
[![Packagist Downloads](https://img.shields.io/packagist/dt/konradmichalik/phpstan-typo3-dead-code?color=brightgreen)](https://packagist.org/packages/konradmichalik/phpstan-typo3-dead-code)

```bash
composer require --dev konradmichalik/phpstan-typo3-dead-code
```

## 🚀 Quick start

With [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer) the detector and the provider are included automatically. Point PHPStan at the extension's code and configuration:

```yaml
# phpstan.neon

parameters:
  paths:
    - Classes
    - Configuration
    - ext_localconf.php
```

```php
// Configuration/Backend/AjaxRoutes.php
'target' => FacetsModalController::class . '::configuration', // FacetsModalController::configuration counts as used
```

See [Usage](docs/usage.md) for the manual setup and for running PHPStan from a subdirectory such as `Tests/CGL`.

## 📚 Documentation

| Topic | What's inside |
|-------|---------------|
| [Usage](docs/usage.md) | Extension installer, manual setup, PHPStan started from a subdirectory, result cache |
| [Entry points](docs/entry-points.md) | Everything the provider marks as used, and what it does not cover |
| [Configuration](docs/configuration.md) | Every option with its default and effect |

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE.md).
