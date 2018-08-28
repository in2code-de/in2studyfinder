<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.in2studyfinder',
    'Pi1',
    ['StudyCourse' => 'list, filter, getCoursesJson'],
    ['StudyCourse' => 'list, filter, getCoursesJson']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.in2studyfinder',
    'Pi2',
    ['StudyCourse' => 'detail'],
    []
);

/**
 * Hook to show PluginInformation under a tt_content element in page module of type powermail
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['in2studyfinder'] =
    \In2code\In2studyfinder\Hooks\PluginPreview::class;

if (\In2code\In2studyfinder\Utility\ConfigurationUtility::isCachingEnabled()) {
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder'] = array();
    }
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['frontend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['frontend'] =
            \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
    }
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['groups'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['in2studyfinder']['groups'] =
            array('pages');
    }
}
