<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Unit;

use KonradMichalik\PhpStanTypo3DeadCode\TypoScriptUserFuncReader;
use PHPUnit\Framework\TestCase;

final class TypoScriptUserFuncReaderTest extends TestCase
{
    public function testReadsUserFuncWithAndWithoutLeadingBackslashAndIgnoresComments(): void
    {
        $functions = (new TypoScriptUserFuncReader())->read(__DIR__ . '/../Fixtures/Project/Configuration/TypoScript/setup.typoscript');

        self::assertSame([
            ['Fixture\Project\ContextUtility', 'getColor'],
            ['Fixture\Project\ContextUtility', 'leadingBackslash'],
        ], $functions);
    }
}
