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

final class DataHandlerHook
{
    public function processDatamap_afterDatabaseOperations(): void {}

    public function processCmdmap_postProcess(): void {}

    public function moveRecord(): void {}

    public function clearCachePostProc(): void {}

    public function notAHook(): void {}
}
