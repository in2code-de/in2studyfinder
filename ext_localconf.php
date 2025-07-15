<?php

declare(strict_types=1);

use In2code\In2studyfinder\Controller\StudyCourseController;
use In2code\In2studyfinder\Hooks\DataHandlerHook;
use In2code\In2studyfinder\Hooks\PluginPreview;
use In2code\In2studyfinder\Updates\StudyCourseSlugUpdater;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
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
    'FastSearch',
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

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['studyCourseSlug']
    = StudyCourseSlugUpdater::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['in2studyfinder_clearcache'] =
    DataHandlerHook::class . '->clearCachePostProc';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['in2studyfinder'][] =
    'In2code\In2studyfinder\ViewHelpers';

/**
 * Hook to show PluginInformation under a tt_content element in page module of type in2studyfinder
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['in2studyfinder']
    = PluginPreview::class;

if (ConfigurationUtility::isCachingEnabled()) {
    if (!is_array(
        ($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'] ?? null)
    )) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'] = [];
    }
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['frontend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['frontend'] =
            VariableFrontend::class;
    }
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['groups'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['groups'] =
            ['pages'];
    }
}
