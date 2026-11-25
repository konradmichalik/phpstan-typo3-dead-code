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

use Fixture\Project\DataHandlerHook;

defined('TYPO3') || exit;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['fixture'] = 'Fixture\Project\DataHandlerHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = DataHandlerHook::class.'->clearCachePostProc';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearPageCacheEval'][] = 'Fixture\Project\ContextUtility';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['other/hook.php']['processDatamapClass'][] = 'Fixture\Project\ContextUtility';
