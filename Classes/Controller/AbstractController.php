<?php

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * AbstractController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AbstractController extends ActionController
{
    /**
     * @var StudyCourseRepository
     */
    protected $studyCourseRepository = null;

    /**
     * @var Logger
     */
    protected $logger = null;

    public function initializeAction()
    {
        $this->studyCourseRepository = $this->setStudyCourseRepository();
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(static::class);
    }

    /**
     * get the studyCourseRepository
     *
     * @return StudyCourseRepository|object
     */
    protected function getStudyCourseRepository()
    {
        return $this->studyCourseRepository;
    }

    /**
     * set the studyCourseRepository
     */
    protected function setStudyCourseRepository()
    {
        $extendedRepositoryName = 'In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseRepository';

        if (ExtensionUtility::isIn2studycoursesExtendLoaded()
            && class_exists($extendedRepositoryName)) {
            return GeneralUtility::makeInstance($extendedRepositoryName);
        }

        return GeneralUtility::makeInstance(StudyCourseRepository::class);
    }
}
