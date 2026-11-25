<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Unit;

use KonradMichalik\PhpStanTypo3DeadCode\PhpConfigurationReader;
use PHPUnit\Framework\TestCase;

final class PhpConfigurationReaderTest extends TestCase
{
    public function testReadsRouteTargetsAsFullyQualifiedNames(): void
    {
        $targets = (new PhpConfigurationReader())->read(__DIR__ . '/../Fixtures/Project/Configuration/Backend/AjaxRoutes.php');

        self::assertSame([
            ['Fixture\Project\ExampleController', 'list'],
            ['Fixture\Project\InvokableController', '__invoke'],
            ['Fixture\Project\ExampleController', 'byString'],
            ['Fixture\Project\ChildController', 'inherited'],
            ['Fixture\Project\TraitController', 'fromTrait'],
            ['fixture\project\examplecontroller', 'CASEINSENSITIVE'],
        ], $targets);
    }

    public function testReadsArrowCallbacksFromTca(): void
    {
        $targets = (new PhpConfigurationReader())->read(__DIR__ . '/../Fixtures/Project/Configuration/TCA/tx_example.php');

        self::assertSame([
            ['Fixture\Project\ContextUtility', 'itemsProc'],
            ['Fixture\Project\ContextUtility', 'byTcaString'],
        ], $targets);
    }

    public function testReadsDataHandlerHookClassesAsTheirHookMethods(): void
    {
        $targets = (new PhpConfigurationReader())->read(__DIR__ . '/../Fixtures/Project/ext_localconf.php');

        self::assertContains(['Fixture\Project\DataHandlerHook', 'processDatamap_afterDatabaseOperations'], $targets);
        self::assertContains(['Fixture\Project\DataHandlerHook', 'processCmdmap_postProcess'], $targets);
        self::assertContains(['Fixture\Project\DataHandlerHook', 'clearCachePostProc'], $targets);
        self::assertNotContains(['Fixture\Project\DataHandlerHook', 'notAHook'], $targets);
    }

    public function testSkipsFilesThatCannotBeParsed(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'dead-code');
        self::assertNotFalse($file);
        file_put_contents($file, '<?php return [');

        try {
            self::assertSame([], (new PhpConfigurationReader())->read($file));
        } finally {
            unlink($file);
        }
    }
}
