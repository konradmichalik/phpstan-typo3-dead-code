<?php

declare(strict_types=1);

namespace Fixture\Project;

use Psr\EventDispatcher\EventDispatcherInterface;

final class ExampleEvent
{
    public array $items;

    public function __construct(public readonly string $payload) {}

    public function getValue(): string
    {
        return '';
    }
}

interface NamedEvent
{
    public function getName(): string;
}

final class ConcreteNamedEvent implements NamedEvent
{
    public function getName(): string
    {
        return '';
    }
}

final class NullsafeEvent
{
    public function getValue(): string
    {
        return '';
    }
}

final class UndispatchedEvent
{
    public function getValue(): string
    {
        return '';
    }
}

final class EventEmitter
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ?EventDispatcherInterface $optionalDispatcher,
    ) {}

    public function __invoke(): void
    {
        $this->dispatcher->dispatch(new ExampleEvent('payload'));
        $this->dispatchNamed(new ConcreteNamedEvent());
        $this->optionalDispatcher?->dispatch(new NullsafeEvent());
        new UndispatchedEvent();
    }

    private function dispatchNamed(NamedEvent $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
