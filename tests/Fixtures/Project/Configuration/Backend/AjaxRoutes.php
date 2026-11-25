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

use Fixture\Project\{ChildController, ExampleController, InvokableController, TraitController};

return [
    'example_list' => [
        'path' => '/example/list',
        'target' => ExampleController::class.'::list',
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
        'target' => ChildController::class.'::inherited',
    ],
    'example_trait' => [
        'path' => '/example/trait',
        'target' => TraitController::class.'::fromTrait',
    ],
    'example_case' => [
        'path' => '/example/case',
        'target' => 'fixture\project\examplecontroller::CASEINSENSITIVE',
    ],
];
