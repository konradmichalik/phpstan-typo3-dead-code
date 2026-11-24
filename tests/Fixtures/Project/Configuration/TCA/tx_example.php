<?php

declare(strict_types=1);

use Fixture\Project\ContextUtility;

return [
    'columns' => [
        'type' => [
            'config' => [
                'itemsProcFunc' => ContextUtility::class . '->itemsProc',
                'userFunc' => 'Fixture\Project\ContextUtility->byTcaString',
            ],
        ],
    ],
];
