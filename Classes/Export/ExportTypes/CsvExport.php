<?php

namespace In2code\In2studyfinder\Export\ExportTypes;

use In2code\In2studyfinder\Export\AbstractExport;
use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class CsvExport extends AbstractExport implements ExportInterface
{
    /**
     * @var string
     */
    protected $fileType = 'csv';

    public function export(array $exportRecords, ExportConfiguration $exportConfiguration)
    {
        $standaloneView = $this->objectManager->get(StandaloneView::class);

        $standaloneView->setLayoutRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName('EXT:in2studyfinder/Resources/Private/Layouts/')
            ]
        );

        $standaloneView->setTemplateRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName('EXT:in2studyfinder/Resources/Private/Templates/')
            ]
        );

        $standaloneView->setPartialRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName('EXT:in2studyfinder/Resources/Private/Partials/')
            ]
        );

        $standaloneView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:in2studyfinder/Resources/Private/Templates/Exporter/Csv.html')
        );

        $recordRows = [];
        foreach ($exportRecords as $row => $record) {
            foreach ($record as $propertyName => $property) {
                $recordRows[$row] .= $property . ';';
            }
            $recordRows[$row] .= PHP_EOL;
        }

        $standaloneView->assign('records', $recordRows);
        return $standaloneView->render();
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }
}
