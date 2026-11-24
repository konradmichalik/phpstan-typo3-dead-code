<?php

declare(strict_types=1);

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
