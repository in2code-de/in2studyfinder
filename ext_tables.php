<?php

defined('TYPO3') or die();

/**
 * Register Icons
 */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);
$iconRegistry->registerIcon(
    'in2studyfinder-plugin-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:in2studyfinder/Resources/Public/Icons/Extension.svg']
);

/**
 * Add to ContentElementWizard
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:in2studyfinder/Configuration/TSConfig/ContentElementWizard.typoscript">'
);

/**
 * Register default export types
 */
$GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes']['CSV'] =
    \In2code\In2studyfinder\Export\ExportTypes\CsvExport::class;
