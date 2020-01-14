<?php

namespace In2code\In2studyfinder\Service;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AbstractService
{
    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->logger = $this->objectManager->get(LogManager::class)->getLogger(static::class);
    }
}
