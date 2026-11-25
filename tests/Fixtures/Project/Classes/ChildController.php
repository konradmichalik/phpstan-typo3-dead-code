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

abstract class BaseController
{
    public function inherited(): void {}
}

final class ChildController extends BaseController {}

trait RoutedTrait
{
    public function fromTrait(): void {}
}

final class TraitController
{
    use RoutedTrait;
}
