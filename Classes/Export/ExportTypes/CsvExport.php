<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Export\ExportTypes;

use In2code\In2studyfinder\Event\ManipulateCsvPropertyBeforeExportEvent;
use In2code\In2studyfinder\Export\AbstractExport;
use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;
use In2code\In2studyfinder\Utility\FileUtility;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CsvExport extends AbstractExport implements ExportInterface
{
    protected string $fileName = 'export.csv';

    /**
     * @throws Exception
     */
    public function export(ExportConfiguration $exportConfiguration): string
    {
        $recordRows = [];
        foreach ($exportConfiguration->getRecordsToExport() as $row => $record) {
            $recordRows[$row] = '';
            foreach ($record as $property) {
                $property = $this->eventDispatcher->dispatch(
                    new ManipulateCsvPropertyBeforeExportEvent($property)
                )->getProperty();

                $recordRows[$row] .= '"' . $property . '";';
            }
            $recordRows[$row] .= PHP_EOL;
        }

        FileUtility::createFolderIfNotExists($exportConfiguration->getExportLocation());

        $fluidVariables = [
            'header' => '"' . implode('";"', $exportConfiguration->getPropertiesToExport()) . '";',
            'records' => $recordRows
        ];

        if (
            !GeneralUtility::writeFile(
                $exportConfiguration->getExportLocation() . $this->fileName,
                $this->getExportFileContent($fluidVariables),
                true
            )
        ) {
            throw new Exception('Export file could not be created!');
        }

        return $exportConfiguration->getExportLocation() . $this->fileName;
    }

    protected function getExportFileContent(array $variables): string
    {
        $standaloneView = $this->getStandaloneView('EXT:in2studyfinder/Resources/Private/Templates/Exporter/Csv.html');

        foreach ($variables as $key => $variable) {
            $standaloneView->assign($key, $variable);
        }

        return trim($standaloneView->render());
    }
}
