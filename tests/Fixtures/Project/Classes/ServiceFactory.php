<?php

declare(strict_types=1);

namespace Fixture\Project;

final class ServiceFactory
{
    public function create(): object
    {
        return new \stdClass();
    }

    public function createNamed(): object
    {
        return new \stdClass();
    }

    public static function createStatic(): object
    {
        return new \stdClass();
    }
}
