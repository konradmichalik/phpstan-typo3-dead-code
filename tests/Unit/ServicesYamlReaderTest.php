<?php

declare(strict_types=1);

/*
 * This file is part of the "phpstan-typo3-dead-code" Composer package.
 *
 * (c) Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Unit;

use KonradMichalik\PhpStanTypo3DeadCode\ServicesYamlReader;
use PHPUnit\Framework\TestCase;

final class ServicesYamlReaderTest extends TestCase
{
    public function testReadsListenerMethodsAndFactories(): void
    {
        $targets = (new ServicesYamlReader())->read(__DIR__.'/../Fixtures/Project/Configuration/Services.yaml');

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
