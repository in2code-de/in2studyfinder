<?php

defined('TYPO3') or die();

(static function () {
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
})();
