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

namespace Fixture\Project;

final class ExampleController
{
    public function __construct(private readonly string $dependency) {}

    public function list(): string
    {
        return $this->dependency;
    }

    public function caseInsensitive(): void {}

    public function neverCalled(): void {}

    public function notExtbaseAction(): void {}
}
