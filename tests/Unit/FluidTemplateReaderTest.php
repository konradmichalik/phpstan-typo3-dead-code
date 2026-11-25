<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Unit;

use KonradMichalik\PhpStanTypo3DeadCode\FluidTemplateReader;
use PHPUnit\Framework\TestCase;

final class FluidTemplateReaderTest extends TestCase
{
    public function testReadsPropertyNamesOfVariablePathsInsideBraces(): void
    {
        $names = (new FluidTemplateReader())->read(__DIR__ . '/../Fixtures/Project/Resources/Private/Templates/Show.html');

        self::assertEqualsCanonicalizing(
            ['title', 'visible', 'author', 'fullName', 'tags', 'identifier', 'published', 'subtitle'],
            $names,
        );
    }
}
