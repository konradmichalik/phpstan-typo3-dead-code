<?php

declare(strict_types=1);

namespace Fixture\Project;

final class ServiceListener
{
    public function onEvent(): void {}

    public function onShortEvent(): void {}

    public function notListening(): void {}
}
