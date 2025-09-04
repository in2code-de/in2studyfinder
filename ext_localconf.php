<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$controller = \In2code\In2studyfinder\Controller\StudyCourseController::class;
$extensionName = 'In2studyfinder';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    $extensionName,
    'Pi1',
    [$controller => 'filter,ajaxFilter'],
    [$controller => 'filter,ajaxFilter']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    $extensionName,
    'FastSearch',
    [$controller => 'fastSearch'],
    []
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    $extensionName,
    'Pi2',
    [$controller => 'detail'],
    []
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
    = \In2code\In2studyfinder\Updates\StudyCourseSlugUpdater::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['in2studyfinder_clearcache'] =
    \In2code\In2studyfinder\Hooks\DataHandlerHook::class . '->clearCachePostProc';

/**
 * Fluid Namespace
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['in2studyfinder'][] = 'In2code\In2studyfinder\ViewHelpers';

/*
Adds the Language Files from in2studyfinder_extend
*/
if (In2code\In2studyfinder\Utility\ExtensionUtility::isIn2studycoursesExtendLoaded()) {
    // Backend
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf'][] =
        'EXT:in2studyfinder_extend/Resources/Private/Language/locallang_db.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:in2studyfinder/Resources/Private/Language/de.locallang_db.xlf'][] =
        'EXT:in2studyfinder_extend/Resources/Private/Language/de.locallang_db.xlf';

    // Frontend
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:in2studyfinder/Resources/Private/Language/locallang.xlf'][] =
        'EXT:in2studyfinder_extend/Resources/Private/Language/locallang.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:in2studyfinder/Resources/Private/Language/de.locallang.xlf'][] =
        'EXT:in2studyfinder_extend/Resources/Private/Language/de.locallang.xlf';
}

/**
 * Hook to show PluginInformation under a tt_content element in page module of type in2studyfinder
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['in2studyfinder']
    = \In2code\In2studyfinder\Hooks\PluginPreview::class;

if (\In2code\In2studyfinder\Utility\ConfigurationUtility::isCachingEnabled()) {
    if (!is_array(($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'] ?? null))) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'] = [];
    }
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['frontend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['frontend'] =
            \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
    }
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['groups'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['groups'] =
            ['pages'];
    }
}
