<?php

declare(strict_types=1);

use Fixture\Project\ExampleController;
use Fixture\Project\InvokableController;

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
];
