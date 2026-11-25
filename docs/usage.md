# Usage

## Extension installer

If you have the [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer)
package installed, there is nothing more to do. The detector and the [provider](../rules.neon)
are included automatically.

## Manual setup

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

Calls from `ext_localconf.php` and `Configuration/` only count when they are analysed.

## PHPStan started from a subdirectory

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

- The provider finds the configuration through the analysed `Configuration` path. Without such a path it falls back to `Configuration/` in the working directory, which is the wrong place here, and every route target and callback is reported. Alternatively, set [`configurationDirectory`](configuration.md#configurationdirectory).
- `--autoload-file` makes the detector recognise overrides of the extension's own dependencies. In a trial `bootstrapFiles` did not help, the command line option did.

With both in place the result matched a run from the project root on four extensions.

## Result cache

PHPStan's result cache does not know that the result depends on configuration, `Services.yaml`, TypoScript and Fluid templates. After a change to one of them alone, the next run can still report the old state. Clear the cache first:

```bash
vendor/bin/phpstan clear-result-cache
```
