<?php

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

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

    public function getFileType()
    {
    }
}
