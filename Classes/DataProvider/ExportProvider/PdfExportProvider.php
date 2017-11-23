<?php

namespace In2code\In2studyfinder\DataProvider\ExportProvider;

use In2code\In2studyfinder\DataProvider\ExportConfiguration;
use In2code\In2studyfinder\DataProvider\ExportProviderInterface;

class PdfExportProvider implements ExportProviderInterface
{
    public function export(array $exportRecords, ExportConfiguration $exportConfiguration)
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($exportRecords, __CLASS__ . ' in der Zeile ' . __LINE__);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($exportConfiguration, __CLASS__ . ' in der Zeile ' . __LINE__);
        die();
        // z.B.
        // $recordsToExport = [
        //                      0 => ExportRecord:1
        //                      1 => ExportRecord:2
        //                    ];
    }
}
