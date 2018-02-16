<?php

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class AbstractExport implements ExportInterface
{
    /**
     * @var ExportConfiguration
     */
    protected $exportConfiguration = null;

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

    public function __construct()
    {
        $this->setObjectManager();
    }

    /**
     * @param ExportConfiguration $exportConfiguration
     */
    public function export(ExportConfiguration $exportConfiguration)
    {
    }

    /**
     *
     */
    public function getFileType()
    {
    }

    /**
     * @return ExportConfiguration
     */
    public function getExportConfiguration()
    {
        return $this->exportConfiguration;
    }

    /**
     * @param ExportConfiguration $exportConfiguration
     */
    public function setExportConfiguration($exportConfiguration)
    {
        $this->exportConfiguration = $exportConfiguration;
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
    )
    {
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
