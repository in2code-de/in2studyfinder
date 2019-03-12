<?php

namespace In2code\In2studyfinder\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
            return $this->objectManager->get($extendedRepositoryName);
        } else {
            return $this->objectManager->get(StudyCourseRepository::class);
        }
    }
}
