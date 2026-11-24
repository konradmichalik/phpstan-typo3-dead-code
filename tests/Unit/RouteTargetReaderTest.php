<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Unit;

use KonradMichalik\PhpStanTypo3DeadCode\RouteTargetReader;
use PHPUnit\Framework\TestCase;

final class RouteTargetReaderTest extends TestCase
{
    public function testResolvesClassConstantsToFullyQualifiedNames(): void
    {
        $targets = (new RouteTargetReader())->read(__DIR__ . '/../Fixtures/Project/Configuration/Backend/AjaxRoutes.php');

        self::assertSame(['list', 'byString'], $targets['Fixture\Project\ExampleController']);
        self::assertSame(['__invoke'], $targets['Fixture\Project\InvokableController']);
        self::assertCount(2, $targets);
    }
}
