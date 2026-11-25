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

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests;

use KonradMichalik\PhpStanTypo3DeadCode\FluidTemplateReader;
use PHPUnit\Framework\TestCase;

final class FluidTemplateReaderTest extends TestCase
{
    public function testReadsPropertyNamesOfVariablePathsInsideBraces(): void
    {
        $names = (new FluidTemplateReader())->read(__DIR__.'/../Fixtures/Project/Resources/Private/Templates/Show.html');

        self::assertEqualsCanonicalizing(
            ['title', 'visible', 'author', 'fullName', 'tags', 'identifier', 'published', 'subtitle'],
            $names,
        );
    }
}
