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

use KonradMichalik\PhpCsFixerPreset\Config;
use KonradMichalik\PhpCsFixerPreset\Rules\Header;
use Symfony\Component\Finder\Finder;

return Config::create()
    ->withRule(
        Header::fromComposer(),
    )
    // Fixtures and stubs model the code under analysis, so fixers must not change their modifiers.
    ->withFinder(static fn (Finder $finder) => $finder->in(__DIR__)->exclude(['tests/Fixtures', 'tests/Stubs']))
;
