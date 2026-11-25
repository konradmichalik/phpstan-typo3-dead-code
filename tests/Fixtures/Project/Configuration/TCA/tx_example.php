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

use Fixture\Project\ContextUtility;

return [
    'columns' => [
        'type' => [
            'config' => [
                'itemsProcFunc' => ContextUtility::class.'->itemsProc',
                'userFunc' => 'Fixture\Project\ContextUtility->byTcaString',
            ],
        ],
    ],
];
