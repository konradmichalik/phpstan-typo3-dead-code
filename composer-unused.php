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

use ComposerUnused\ComposerUnused\Configuration\{Configuration, NamedFilter};

return static function (Configuration $config): Configuration {
    // PHPStan ships its classes inside phpstan.phar and declares no namespace, so composer-unused cannot map them.
    return $config
        ->addNamedFilter(NamedFilter::fromString('phpstan/phpstan'));
};
