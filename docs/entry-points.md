# Entry points

| Source | Marked as used |
|--------|----------------|
| `Configuration/**/*.php` | Route targets `Foo::class . '::bar'` and `'Vendor\Foo::bar'`, an invokable `'target' => Foo::class` (`__invoke`), and TCA callbacks such as `'itemsProcFunc' => Foo::class . '->bar'` or `'userFunc' => 'Vendor\Foo->bar'` |
| `Configuration/**/*.typoscript` | `userFunc = Vendor\Foo->bar`, comment lines excluded |
| `Configuration/**/*.yaml` | In the `services` section: the `method` of a tag, such as an `event.listener` with `method: onEvent`, and a `factory` written as `['@Vendor\Foo', 'create']` or `['Vendor\Foo', 'create']`. A factory service registered under a name such as `@my.factory` is resolved through its `class` |
| `ext_localconf.php`, `ext_tables.php` | The same targets as in `Configuration/**/*.php`, such as `'clearCachePostProc'][] = Foo::class . '->bar'`, and DataHandler hook classes registered as `processDatamapClass`, `processCmdmapClass`, `moveRecordClass` or `checkModifyAccessList`, whose hook methods such as `processDatamap_afterDatabaseOperations` count as used |
| Any class | `__construct`, because the container instantiates it |
| Any class | Public `inject*` methods and a public `initializeObject`, which the container calls on autowired services. Both rows follow [`markConstructors`](configuration.md#markconstructors) |
| Extbase controllers | Every `*Action` method, `initialize*Action` included, of a class extending `ActionController` |
| Dispatched events | Public methods and properties, with [`markEventApi`](configuration.md#markeventapi) |
| Fluid templates | Getters and public properties named in variable paths, with [`markFluidAccess`](configuration.md#markfluidaccess) |

```php
// Configuration/Backend/AjaxRoutes.php
'target' => FacetsModalController::class . '::configuration', // FacetsModalController::configuration counts as used
```

Class names are resolved to fully qualified names, so two classes with the same short name do not cover each other. A route that names a child class also covers a method declared in its parent class or in a trait. A PHP file that cannot be parsed is skipped.

## Not covered

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
