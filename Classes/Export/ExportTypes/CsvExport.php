<?php

namespace In2code\In2studyfinder\Export\ExportTypes;

use In2code\In2studyfinder\Export\AbstractExport;
use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;
use In2code\In2studyfinder\Utility\FileUtility;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class CsvExport extends AbstractExport implements ExportInterface
{
    /**
     * @var string
     */
    protected $fileType = 'csv';

    /**
     * @param ExportConfiguration $exportConfiguration
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    public function export(ExportConfiguration $exportConfiguration)
    {
        $this->setExportConfiguration($exportConfiguration);

        $recordRows = [];
        foreach ($this->exportConfiguration->getRecordsToExport() as $row => $record) {
            foreach ($record as $propertyName => $property) {
                $recordRows[$row] .= $property . ';';
            }
            $recordRows[$row] .= PHP_EOL;
        }

        $fileName = 'test.csv';

        FileUtility::createFolderIfNotExists($this->exportConfiguration->getExportLocation());

        if (!GeneralUtility::writeFile($this->exportConfiguration->getExportLocation() . $fileName, $this->getExportFileContent($recordRows), true)) {
            throw new Exception('Export file could not be created!');
        }

        return $this->exportConfiguration->getExportLocation() . $fileName;
    }

    /**
     * @param $recordRows
     * @return string
     */
    protected function getExportFileContent($recordRows)
    {
        $standaloneView = $this->getStandaloneView('EXT:in2studyfinder/Resources/Private/Templates/Exporter/Csv.html');

        $standaloneView->assign('records', $recordRows);

        return trim($standaloneView->render());
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }
}