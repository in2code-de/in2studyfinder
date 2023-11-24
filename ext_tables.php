<?php

defined('TYPO3') or die();

(static function () {
    /**
     * Register default export types
     */
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes']['CSV'] =
        \In2code\In2studyfinder\Export\ExportTypes\CsvExport::class;
})();
