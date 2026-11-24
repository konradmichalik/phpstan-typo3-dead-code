<?php

declare(strict_types=1);

namespace Fixture\Project;

final class ExampleController
{
    public function __construct(private readonly string $dependency) {}

    public function list(): string
    {
        return $this->dependency;
    }

    public function neverCalled(): void {}
}
