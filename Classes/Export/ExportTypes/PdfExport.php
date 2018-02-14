<?php

namespace In2code\In2studyfinder\Export\ExportTypes;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;

class PdfExport implements ExportInterface
{
    /**
     * @var string
     */
    protected $fileType = 'pdf';

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

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }
}
