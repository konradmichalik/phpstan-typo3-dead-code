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

final class ContextUtility
{
    public function getColor(): string
    {
        return 'red';
    }

    public function commentedOut(): void {}

    public function itemsProc(): void {}

    public function byTcaString(): void {}

    public function deadUtility(): void {}

    /**
     * @api
     */
    public function publicApi(): void {}
}
