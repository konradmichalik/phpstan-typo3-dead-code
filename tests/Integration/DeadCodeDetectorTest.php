<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Integration;

use PHPUnit\Framework\TestCase;

final class DeadCodeDetectorTest extends TestCase
{
    private const DEAD = [
        'Unused Fixture\Other\ExampleController::list',
        'Unused Fixture\Project\BlogController::renderTeaser',
        'Unused Fixture\Project\ContextUtility::commentedOut',
        'Unused Fixture\Project\ContextUtility::deadUtility',
        'Unused Fixture\Project\DataHandlerHook::notAHook',
        'Unused Fixture\Project\ExampleController::neverCalled',
        'Unused Fixture\Project\ExampleController::notExtbaseAction',
        'Unused Fixture\Project\ExampleRepository::injectHidden',
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

    public function testConstructorsAreReportedWhenNotMarked(): void
    {
        self::assertContains(
            'Unused Fixture\Project\ExampleController::__construct',
            $this->analyse('phpstan.constructors.neon'),
        );
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
