<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode\Tests\Integration;

use PHPUnit\Framework\TestCase;

final class DeadCodeDetectorTest extends TestCase
{
    public function testOnlyGenuinelyDeadMembersAreReported(): void
    {
        self::assertSame([
            'Unused Fixture\Other\ExampleController::list',
            'Unused Fixture\Project\ContextUtility::deadUtility',
            'Unused Fixture\Project\ExampleController::neverCalled',
        ], $this->analyse('phpstan.neon'));
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
    private function analyse(string $configuration): array
    {
        $root = dirname(__DIR__, 2);
        $command = sprintf(
            'cd %s && %s vendor/bin/phpstan analyse -c %s --autoload-file=vendor/autoload.php --no-progress --error-format=raw --memory-limit=1G 2>&1',
            escapeshellarg($root),
            escapeshellarg(PHP_BINARY),
            escapeshellarg('tests/Integration/' . $configuration),
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
