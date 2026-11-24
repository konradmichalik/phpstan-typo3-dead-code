<?php

declare(strict_types=1);

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
