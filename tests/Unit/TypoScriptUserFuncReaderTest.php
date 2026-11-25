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

use KonradMichalik\PhpStanTypo3DeadCode\TypoScriptUserFuncReader;
use PHPUnit\Framework\TestCase;

final class TypoScriptUserFuncReaderTest extends TestCase
{
    public function testReadsUserFuncWithAndWithoutLeadingBackslashAndIgnoresComments(): void
    {
        $functions = (new TypoScriptUserFuncReader())->read(__DIR__.'/../Fixtures/Project/Configuration/TypoScript/setup.typoscript');

        self::assertSame([
            ['Fixture\Project\ContextUtility', 'getColor'],
            ['Fixture\Project\ContextUtility', 'leadingBackslash'],
        ], $functions);
    }
}
