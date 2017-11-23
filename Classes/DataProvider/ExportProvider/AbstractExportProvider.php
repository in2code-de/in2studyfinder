<?php

namespace In2code\In2studyfinder\DataProvider\ExportProvider;

use In2code\In2studyfinder\DataProvider\ExportConfiguration;
use In2code\In2studyfinder\DataProvider\ExportProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class AbstractExportProvider implements ExportProviderInterface
{
    /**
     * @var ExportConfiguration
     */
    protected $exportConfiguration = null;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    public function __construct()
    {
        $this->setObjectManager();
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

    public function export(array $exportRecords, ExportConfiguration $exportConfiguration)
    {
    }
}
