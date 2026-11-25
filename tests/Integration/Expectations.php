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

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Integration;

/**
 * Sorted detector messages for tests/Fixtures/Project, shared by the in-process and the command line tests.
 */
final class Expectations
{
    // The detector honours @api only inside analysedPaths, which the in-process test container leaves empty.
    public const PUBLIC_API_IN_PROCESS = 'Unused Fixture\Project\ContextUtility::publicApi';

    public const DEAD = [
        'Property Fixture\Project\ExampleEvent::$items is never read',
        'Property Fixture\Project\ExampleEvent::$items is never written',
        'Property Fixture\Project\ExampleEvent::$payload is never read',
        'Property Fixture\Project\FluidRecord::$identifier is never read',
        'Property Fixture\Project\FluidRecord::$tags is never read',
        'Property Fixture\Project\FluidRecord::$word is never read',
        'Unused Fixture\Other\ExampleController::list',
        'Unused Fixture\Project\BlogController::renderTeaser',
        'Unused Fixture\Project\ConcreteNamedEvent::getName',
        'Unused Fixture\Project\ContextUtility::commentedOut',
        'Unused Fixture\Project\ContextUtility::deadUtility',
        'Unused Fixture\Project\DataHandlerHook::notAHook',
        'Unused Fixture\Project\ExampleController::neverCalled',
        'Unused Fixture\Project\ExampleController::notExtbaseAction',
        'Unused Fixture\Project\ExampleEvent::getValue',
        'Unused Fixture\Project\ExampleRepository::injectHidden',
        'Unused Fixture\Project\FluidRecord::getAuthor',
        'Unused Fixture\Project\FluidRecord::getHeader',
        'Unused Fixture\Project\FluidRecord::getSecret',
        'Unused Fixture\Project\FluidRecord::getSubtitle',
        'Unused Fixture\Project\FluidRecord::getSummary',
        'Unused Fixture\Project\FluidRecord::getTitle',
        'Unused Fixture\Project\FluidRecord::hasTags',
        'Unused Fixture\Project\FluidRecord::isPublished',
        'Unused Fixture\Project\FluidRecord::isVisible',
        'Unused Fixture\Project\NamedEvent::getName',
        'Unused Fixture\Project\NullsafeEvent::getValue',
        'Unused Fixture\Project\ServiceListener::notListening',
        'Unused Fixture\Project\UndispatchedEvent::getValue',
    ];

    public const EVENT_API = [
        'Property Fixture\Project\ExampleEvent::$items is never read',
        'Property Fixture\Project\ExampleEvent::$items is never written',
        'Property Fixture\Project\ExampleEvent::$payload is never read',
        'Unused Fixture\Project\ConcreteNamedEvent::getName',
        'Unused Fixture\Project\ExampleEvent::getValue',
        'Unused Fixture\Project\NamedEvent::getName',
        'Unused Fixture\Project\NullsafeEvent::getValue',
    ];

    public const FLUID_ACCESS = [
        'Property Fixture\Project\FluidRecord::$identifier is never read',
        'Unused Fixture\Project\FluidRecord::getAuthor',
        'Unused Fixture\Project\FluidRecord::getSubtitle',
        'Unused Fixture\Project\FluidRecord::getSummary',
        'Unused Fixture\Project\FluidRecord::getTitle',
        'Unused Fixture\Project\FluidRecord::hasTags',
        'Unused Fixture\Project\FluidRecord::isPublished',
        'Unused Fixture\Project\FluidRecord::isVisible',
    ];

    public const CONTAINER_CALLS = [
        'Unused Fixture\Project\EventEmitter::__construct',
        'Unused Fixture\Project\ExampleController::__construct',
        'Unused Fixture\Project\ExampleRepository::initializeObject',
        'Unused Fixture\Project\ExampleRepository::injectConnectionPool',
    ];
}
