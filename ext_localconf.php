<?php

defined('TYPO3') or die();

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'In2studyfinder',
        'Pi1',
        [\In2code\In2studyfinder\Controller\StudyCourseController::class => 'filter,ajaxFilter'],
        [\In2code\In2studyfinder\Controller\StudyCourseController::class => 'filter,ajaxFilter']
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'In2studyfinder',
        'FastSearch',
        [\In2code\In2studyfinder\Controller\StudyCourseController::class => 'fastSearch'],
        []
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'In2studyfinder',
        'Pi2',
        [\In2code\In2studyfinder\Controller\StudyCourseController::class => 'detail'],
        []
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['studyCourseSlug']
        = \In2code\In2studyfinder\Updates\StudyCourseSlugUpdater::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['in2studyfinder_clearcache'] =
        \In2code\In2studyfinder\Hooks\DataHandlerHook::class . '->clearCachePostProc';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['in2studyfinder'][] =
        'In2code\In2studyfinder\ViewHelpers';

    /**
     * Hook to show PluginInformation under a tt_content element in page module of type in2studyfinder
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['in2studyfinder']
        = \In2code\In2studyfinder\Hooks\PluginPreview::class;

    if (\In2code\In2studyfinder\Utility\ConfigurationUtility::isCachingEnabled()) {
        if (!is_array(
            ($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'] ?? null)
        )) {
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
})();
