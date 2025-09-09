<?php

defined('TYPO3') or die();

(static function () {
    // hooking into TCE Main to monitor studycourse updates that may require updating their embeddings
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \In2code\In2studyfinder\Hooks\EmbeddingsMonitor::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \In2code\In2studyfinder\Hooks\EmbeddingsMonitor::class;
})();
