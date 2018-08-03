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
    protected $fileName = 'export.csv';

    /**
     * @param ExportConfiguration $exportConfiguration
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    public function export(ExportConfiguration $exportConfiguration)
    {
        $recordRows = [];
        foreach ($exportConfiguration->getRecordsToExport() as $row => $record) {
            foreach ($record as $propertyName => $property) {
                $recordRows[$row] .= $property . ';';
            }
            $recordRows[$row] .= PHP_EOL;
        }

        FileUtility::createFolderIfNotExists($exportConfiguration->getExportLocation());

        $fluidVariables = [
            'records' => $recordRows
        ];

        if (!GeneralUtility::writeFile(
            $exportConfiguration->getExportLocation() . $this->fileName,
            $this->getExportFileContent($fluidVariables),
            true
        )) {
            throw new Exception('Export file could not be created!');
        }

        return $exportConfiguration->getExportLocation() . $this->fileName;
    }

    /**
     * @param array $variables
     * @return string
     */
    protected function getExportFileContent(array $variables)
    {
        $standaloneView = $this->getStandaloneView('EXT:in2studyfinder/Resources/Private/Templates/Exporter/Csv.html');

        foreach ($variables as $key => $variable) {
            $standaloneView->assign($key, $variable);
        }

        return trim($standaloneView->render());
    }
}
