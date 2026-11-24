<?php

declare(strict_types=1);

use Fixture\Project\ChildController;
use Fixture\Project\ExampleController;
use Fixture\Project\InvokableController;
use Fixture\Project\TraitController;

return [
    'example_list' => [
        'path' => '/example/list',
        'target' => ExampleController::class . '::list',
    ],
    'example_invokable' => [
        'path' => '/example/invokable',
        'target' => InvokableController::class,
    ],
    'example_string' => [
        'path' => '/example/string',
        'target' => 'Fixture\Project\ExampleController::byString',
    ],
    'example_inherited' => [
        'path' => '/example/inherited',
        'target' => ChildController::class . '::inherited',
    ],
    'example_trait' => [
        'path' => '/example/trait',
        'target' => TraitController::class . '::fromTrait',
    ],
];
