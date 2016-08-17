<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extKey = 'in2studyfinder';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.' . $extKey,
    'Pi1',
    array(
        'StudyCourse' => 'list, filter',
    ),
    // non-cacheable actions
    array(
        'StudyCourse' => 'list, filter',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.' . $extKey,
    'Pi2',
    array(
        'StudyCourse' => 'detail',
    ),
    // non-cacheable actions
    array(
        'StudyCourse' => 'detail',
    )
);

/**
 * Hook to show PluginInformation under a tt_content element in page module of type powermail
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$extKey] =
    \In2code\In2studyfinder\Hooks\PluginPreview::class;
