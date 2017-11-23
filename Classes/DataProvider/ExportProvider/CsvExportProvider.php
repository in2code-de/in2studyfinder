<?php

namespace In2code\In2studyfinder\DataProvider\ExportProvider;

use In2code\In2studyfinder\DataProvider\ExportConfiguration;
use In2code\In2studyfinder\DataProvider\ExportProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class CsvExportProvider extends AbstractExportProvider implements ExportProviderInterface
{
    public function export(array $exportRecords, ExportConfiguration $exportConfiguration)
    {
        $standaloneView = $this->objectManager->get(StandaloneView::class);
        //$standaloneView->setFormat('csv');
        $standaloneView->setLayoutRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName('EXT:in2studyfinder/Resources/Private/Layouts/')
            ]
        );
        $standaloneView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:in2studyfinder/Resources/Private/Templates/Exporter/Csv.html')
        );

        $standaloneView->assign('records', $exportRecords);
        return $standaloneView->render();
    }


}
