<?php

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\StandaloneView;

class AbstractExport implements ExportInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var string
     */
    protected $contentType = 'application/force-download';

    /**
     * @var string
     */
    protected $fileExtension = '';

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    public function __construct()
    {
        $this->setObjectManager();
        $this->signalSlotDispatcher = $this->objectManager->get(Dispatcher::class);
    }

    /**
     * @param ExportConfiguration $exportConfiguration
     */
    public function export(ExportConfiguration $exportConfiguration)
    {
    }

    protected function setObjectManager()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @param $templateFilePath
     * @param string $layoutPath
     * @param string $templatePath
     * @param string $partialPath
     * @return StandaloneView
     */
    protected function getStandaloneView(
        $templateFilePath,
        $layoutPath = 'EXT:in2studyfinder/Resources/Private/Layouts/',
        $templatePath = 'EXT:in2studyfinder/Resources/Private/Templates/',
        $partialPath = 'EXT:in2studyfinder/Resources/Private/Partials/'
    ) {
        $standaloneView = $this->objectManager->get(StandaloneView::class);

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
