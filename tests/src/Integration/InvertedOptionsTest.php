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

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Integration;

use function array_diff;
use function array_merge;
use function array_values;

final class InvertedOptionsTest extends InProcessTestCase
{
    public function testEveryOptionChangesItsMembers(): void
    {
        $expected = array_merge(
            array_values(array_diff(Expectations::DEAD, Expectations::EVENT_API, Expectations::FLUID_ACCESS)),
            Expectations::CONTAINER_CALLS,
        );

        self::assertSame(self::withPublicApi($expected), $this->analyseFixtures());
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__.'/inprocess.options.neon'];
    }
}
