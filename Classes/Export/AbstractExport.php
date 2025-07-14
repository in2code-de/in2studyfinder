<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class AbstractExport implements ExportInterface
{
    protected string $fileExtension = '';

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct()
    {
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
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

        $standaloneView->getRenderingContext()->getTemplatePaths()->setLayoutRootPaths([
            0 => GeneralUtility::getFileAbsFileName($layoutPath)
        ]);

        $standaloneView->getRenderingContext()->getTemplatePaths()->setTemplateRootPaths([
            0 => GeneralUtility::getFileAbsFileName($templatePath)
        ]);

        $standaloneView->getRenderingContext()->getTemplatePaths()->setPartialRootPaths([
            0 => GeneralUtility::getFileAbsFileName($partialPath)
        ]);

        $standaloneView->getRenderingContext()->getTemplatePaths()->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateFilePath));

        return $standaloneView;
    }
}
