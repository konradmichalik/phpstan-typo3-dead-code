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

use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use ShipMonk\PHPStan\DeadCode\Rule\DeadCodeRule;

use function dirname;

/**
 * Runs the detector with this package's providers inside the test process, so their code counts for coverage.
 *
 * @extends RuleTestCase<DeadCodeRule>
 */
abstract class InProcessTestCase extends RuleTestCase
{
    /**
     * The test container ignores scanDirectories, so the stubs are loaded before the first class lookup. They stay
     * out of the autoloader, because the command line tests check that nothing depends on runtime autoloading.
     *
     * @return list<string>
     */
    protected function analyseFixtures(): array
    {
        foreach (glob(dirname(__DIR__, 2).'/Stubs/*.php') ?: [] as $stub) {
            require_once $stub;
        }

        $messages = [];
        foreach ($this->gatherAnalyserErrors(glob(dirname(__DIR__, 2).'/Fixtures/Project/Classes/*.php') ?: []) as $error) {
            $messages[] = $error->getMessage();
        }
        sort($messages);

        return $messages;
    }

    /**
     * @param list<string> $messages
     *
     * @return list<string>
     */
    protected static function withPublicApi(array $messages): array
    {
        $messages[] = Expectations::PUBLIC_API_IN_PROCESS;
        sort($messages);

        return $messages;
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(DeadCodeRule::class);
    }

    protected function getCollectors(): array
    {
        return array_values(array_filter(
            self::getContainer()->getServicesByTag('phpstan.collector'),
            static fn (mixed $service): bool => $service instanceof Collector,
        ));
    }
}
