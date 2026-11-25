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

use PHPUnit\Framework\TestCase;

use function dirname;
use function sprintf;

/**
 * Runs PHPStan on the command line for what the in-process tests cannot reproduce: locating the configuration
 * directory from the neon file, analysedPaths or the working directory, and Extbase known only through scanDirectories.
 */
final class DeadCodeDetectorTest extends TestCase
{
    public function testRelativeConfigurationDirectoryIsResolvedFromTheNeonFile(): void
    {
        self::assertSame(Expectations::DEAD, $this->analyse('phpstan.explicit.neon', sys_get_temp_dir()));
    }

    public function testConfigurationDirectoryIsDerivedFromTheAnalysedPathsWhateverTheWorkingDirectory(): void
    {
        self::assertSame(Expectations::DEAD, $this->analyse('phpstan.derived.neon', sys_get_temp_dir()));
    }

    public function testFallsBackToTheConfigurationDirectoryOfTheWorkingDirectory(): void
    {
        self::assertSame(Expectations::DEAD, $this->analyse('phpstan.fallback.neon', dirname(__DIR__, 2).'/Fixtures/Project'));
    }

    /**
     * @return list<string>
     */
    private function analyse(string $configuration, ?string $workingDirectory = null): array
    {
        $root = dirname(__DIR__, 3);
        $command = sprintf(
            'cd %s && %s %s analyse -c %s --autoload-file=%s --no-progress --error-format=raw --memory-limit=1G 2>&1',
            escapeshellarg($workingDirectory ?? $root),
            escapeshellarg(\PHP_BINARY),
            escapeshellarg($root.'/vendor/bin/phpstan'),
            escapeshellarg(__DIR__.'/'.$configuration),
            escapeshellarg($root.'/vendor/autoload.php'),
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
