# Configuration options

```yaml
parameters:
  typo3DeadCode:
    configurationDirectory: ../../Configuration
    markConstructors: true
    markEventApi: false
    markFluidAccess: false
```

## `configurationDirectory`

Default: none

Directory scanned for PHP, TypoScript and YAML files. `ext_localconf.php` and `ext_tables.php` next to it are read as well. A relative path is resolved from the directory of the `.neon` file that sets it.

Without it, the analysed path named `Configuration` is used, or `Configuration/` in the working directory if there is none.

## `markConstructors`

Default: `true`

Treats every constructor, public `inject*` method and public `initializeObject` as used. Set `false` to let the detector report them, which is often wrong without a container that creates every class.

## `markEventApi`

Default: `false`

Treats the public methods and properties of an event as used wherever it is passed to `EventDispatcherInterface::dispatch()`, because listeners in other packages read them and write public properties that are not readonly. Dispatched through an interface or base class, the implementations and overrides of its members count too.

This also hides getters nobody reads, so it is off by default. Members that only a subclass declares and objects returned by the getters are not covered.

## `markFluidAccess`

Default: `false`

Reads every `*.html` and `*.txt` under `Resources/Private/` next to the configuration directory and treats a public property `$foo` and public methods `getFoo()`, `isFoo()` and `hasFoo()` as used when a variable path such as `{record.foo}` names `foo`. Quoted strings such as translation keys do not count.

The match is by name only, because the template does not say which class `record` is. A generic name such as `value` or `date` therefore also hides that member on unrelated classes, so it is off by default.
