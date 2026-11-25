<?php

declare(strict_types=1);

namespace Fixture\Project;

final class DataHandlerHook
{
    public function processDatamap_afterDatabaseOperations(): void {}

    public function processCmdmap_postProcess(): void {}

    public function moveRecord(): void {}

    public function clearCachePostProc(): void {}

    public function notAHook(): void {}
}
