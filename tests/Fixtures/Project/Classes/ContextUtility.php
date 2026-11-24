<?php

declare(strict_types=1);

namespace Fixture\Project;

final class ContextUtility
{
    public function getColor(): string
    {
        return 'red';
    }

    public function deadUtility(): void {}

    /**
     * @api
     */
    public function publicApi(): void {}
}
