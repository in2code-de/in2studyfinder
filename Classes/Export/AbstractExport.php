<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\StandaloneView;

class AbstractExport implements ExportInterface
{

    protected string $fileExtension = '';

    /**
     * @var Dispatcher
     */
    protected Dispatcher $signalSlotDispatcher;

    public function __construct()
    {
        // @todo replace signal with psr 14 event
        $this->signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
    }

    public function export(ExportConfiguration $exportConfiguration): string
    {
    }

    protected function getStandaloneView(
        string $templateFilePath,
        string $layoutPath = 'EXT:in2studyfinder/Resources/Private/Layouts/',
        string $templatePath = 'EXT:in2studyfinder/Resources/Private/Templates/',
        string $partialPath = 'EXT:in2studyfinder/Resources/Private/Partials/'
    ): StandaloneView {
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);

        $standaloneView->setLayoutRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName($layoutPath)
            ]
        );

        $standaloneView->setTemplateRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName($templatePath)
            ]
        );

        $standaloneView->setPartialRootPaths(
            [
                0 => GeneralUtility::getFileAbsFileName($partialPath)
            ]
        );

        $standaloneView->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName($templateFilePath)
        );

        return $standaloneView;
    }
}
