<?php

declare(strict_types=1);

use In2code\In2studyfinder\Controller\StudyCourseController;
use In2code\In2studyfinder\Hooks\DataHandlerHook;
use In2code\In2studyfinder\Updates\StudyCourseSlugUpdater;
use In2code\In2studyfinder\Utility\CacheUtility;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
    'In2studyfinder',
    'Filter',
    [StudyCourseController::class => 'filter,ajaxFilter'],
    [StudyCourseController::class => 'filter,ajaxFilter'],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'In2studyfinder',
    'Fastsearch',
    [StudyCourseController::class => 'fastSearch'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'In2studyfinder',
    'Detail',
    [StudyCourseController::class => 'detail'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'in2studyfinder',
    'Chatbot',
    [
        \In2code\In2studyfinder\Controller\ChatController::class => 'index,chat,deleteHistory',
    ],
    // non cacheable actions
    [
        \In2code\In2studyfinder\Controller\ChatController::class => 'chat,deleteHistory',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['studyCourseSlug']
    = StudyCourseSlugUpdater::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['in2studyfinder_clearcache'] =
    DataHandlerHook::class . '->clearCachePostProc';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['in2studyfinder'][] =
    'In2code\In2studyfinder\ViewHelpers';

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][CacheUtility::CACHE_NAME] ?? null)) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][CacheUtility::CACHE_NAME] = [
        'frontend' => VariableFrontend::class,
        'backend' => Typo3DatabaseBackend::class,
        'groups' => ['pages'],
        'options' => [
            'defaultLifetime' => 3600,
        ]
    ];
}
