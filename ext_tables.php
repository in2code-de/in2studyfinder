<?php

defined('TYPO3') or die();

(static function () {
    /**
     * Register default export types
     */
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes']['CSV'] =
        \In2code\In2studyfinder\Export\ExportTypes\CsvExport::class;

    // hooking into TCE Main to monitor studycourse updates that may require updating their embeddings
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \In2code\In2studyfinder\Hooks\EmbeddingsMonitor::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \In2code\In2studyfinder\Hooks\EmbeddingsMonitor::class;
})();
