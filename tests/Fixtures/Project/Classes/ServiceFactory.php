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

namespace Fixture\Project;

use stdClass;

final class ServiceFactory
{
    public function create(): object
    {
        return new stdClass();
    }

    public function createNamed(): object
    {
        return new stdClass();
    }

    public static function createStatic(): object
    {
        return new stdClass();
    }
}
