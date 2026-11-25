<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Integration;

use PHPUnit\Framework\TestCase;

final class DeadCodeDetectorTest extends TestCase
{
    private const EVENT_API = [
        'Property Fixture\Project\ExampleEvent::$items is never read',
        'Property Fixture\Project\ExampleEvent::$items is never written',
        'Property Fixture\Project\ExampleEvent::$payload is never read',
        'Unused Fixture\Project\ConcreteNamedEvent::getName',
        'Unused Fixture\Project\ExampleEvent::getValue',
        'Unused Fixture\Project\NamedEvent::getName',
        'Unused Fixture\Project\NullsafeEvent::getValue',
    ];

    private const FLUID_ACCESS = [
        'Property Fixture\Project\FluidRecord::$identifier is never read',
        'Unused Fixture\Project\FluidRecord::getAuthor',
        'Unused Fixture\Project\FluidRecord::getSubtitle',
        'Unused Fixture\Project\FluidRecord::getSummary',
        'Unused Fixture\Project\FluidRecord::getTitle',
        'Unused Fixture\Project\FluidRecord::hasTags',
        'Unused Fixture\Project\FluidRecord::isPublished',
        'Unused Fixture\Project\FluidRecord::isVisible',
    ];

    private const DEAD = [
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

    public function testOnlyGenuinelyDeadMembersAreReported(): void
    {
        self::assertSame(self::DEAD, $this->analyse('phpstan.neon'));
    }

    public function testConfigurationDirectoryIsDerivedFromTheAnalysedPathsWhateverTheWorkingDirectory(): void
    {
        self::assertSame(self::DEAD, $this->analyse('phpstan.derived.neon', sys_get_temp_dir()));
    }

    public function testFallsBackToTheConfigurationDirectoryOfTheWorkingDirectory(): void
    {
        self::assertSame(self::DEAD, $this->analyse('phpstan.fallback.neon', dirname(__DIR__) . '/Fixtures/Project'));
    }

    public function testPublicMembersOfDispatchedEventsAreUsedWhenEnabled(): void
    {
        self::assertSame(array_values(array_diff(self::DEAD, self::EVENT_API)), $this->analyse('phpstan.events.neon'));
    }

    public function testMembersReadInFluidTemplatesAreUsedWhenEnabled(): void
    {
        self::assertSame(array_values(array_diff(self::DEAD, self::FLUID_ACCESS)), $this->analyse('phpstan.fluid.neon'));
    }

    public function testContainerCallsAreReportedWhenConstructorsAreNotMarked(): void
    {
        $messages = $this->analyse('phpstan.constructors.neon');

        self::assertContains('Unused Fixture\Project\ExampleController::__construct', $messages);
        self::assertContains('Unused Fixture\Project\ExampleRepository::injectConnectionPool', $messages);
        self::assertContains('Unused Fixture\Project\ExampleRepository::initializeObject', $messages);
    }

    /**
     * @return list<string>
     */
    private function analyse(string $configuration, ?string $workingDirectory = null): array
    {
        $root = dirname(__DIR__, 2);
        $command = sprintf(
            'cd %s && %s %s analyse -c %s --autoload-file=%s --no-progress --error-format=raw --memory-limit=1G 2>&1',
            escapeshellarg($workingDirectory ?? $root),
            escapeshellarg(PHP_BINARY),
            escapeshellarg($root . '/vendor/bin/phpstan'),
            escapeshellarg(__DIR__ . '/' . $configuration),
            escapeshellarg($root . '/vendor/autoload.php'),
        );

        // PHPStan may print a preamble before the report, so only lines shaped like "file:line:message" count.
        $messages = [];
        foreach (explode("\n", (string) shell_exec($command)) as $line) {
            if (1 === preg_match('/^\S+\.php:\d+:(.+?)( \[identifier=[\w.]+\])?$/', $line, $match)) {
                $messages[] = $match[1];
            }
        }
        sort($messages);

        return $messages;
    }
}
