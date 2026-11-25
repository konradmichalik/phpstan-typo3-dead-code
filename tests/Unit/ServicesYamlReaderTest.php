<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Unit;

use KonradMichalik\PhpStanTypo3DeadCode\ServicesYamlReader;
use PHPUnit\Framework\TestCase;

final class ServicesYamlReaderTest extends TestCase
{
    public function testReadsListenerMethodsAndFactories(): void
    {
        $targets = (new ServicesYamlReader())->read(__DIR__ . '/../Fixtures/Project/Configuration/Services.yaml');

        self::assertSame([
            ['Fixture\Project\ServiceListener', 'onEvent'],
            ['Fixture\Project\ServiceListener', 'onShortEvent'],
            ['Fixture\Project\ServiceFactory', 'create'],
            ['Fixture\Project\ServiceFactory', 'createStatic'],
            ['Fixture\Project\ServiceFactory', 'createNamed'],
        ], $targets);
    }

    public function testSkipsFilesThatCannotBeParsed(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'dead-code');
        self::assertNotFalse($file);
        file_put_contents($file, "services:\n  - [unclosed");

        try {
            self::assertSame([], (new ServicesYamlReader())->read($file));
        } finally {
            unlink($file);
        }
    }
}
