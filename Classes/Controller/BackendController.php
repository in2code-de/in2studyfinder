<?php

namespace In2code\In2studyfinder\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
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

use In2code\In2studyfinder\DataProvider\ExportConfiguration;
use In2code\In2studyfinder\DataProvider\ExportProvider\CsvExportProvider;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Service\ExportService;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class BackendController extends AbstractController
{
    public function initializeAction()
    {
        parent::initializeAction();


        if ($this->settings['storagePid'] === '') {
            // @Todo: Add Warning Message!
        }

        if ($this->settings['settingsPid'] === '') {
            // @Todo: Add Warning Message!
        }
    }

    public function listAction()
    {
        $studyCourses = $this->getStudyCourseRepository()->findAll();

        $possibleExportDataProvider = $this->getPossibleExportDataProvider();
        $possibleFieldsToExport = $this->getPossibleFieldsToExportForStudyCourses($studyCourses[0]);

        $this->view->assignMultiple(
            [
                'studyCourses' => $studyCourses,
                'exportDataProvider' => $possibleExportDataProvider
            ]
        );
    }

    public function getPossibleExportDataProvider()
    {
        $possibleDataProvider = [];

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportProvider'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportProvider'])
        ) {
            $possibleDataProvider = $GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportProvider'];
        }

        return $possibleDataProvider;
    }

    public function exportAction($studyCourses)
    {
        // Begin Export

        /*
         * Was muss exportiert werden
         *
         *  - welche Felder
         *  - welche Models
         *  - welche Studiengänge
         *  - als was soll exportiert werden
         */

        $exporterConfiguration = $this->objectManager->get(ExportConfiguration::class);
        //$exporterConfiguration->setFields(['uid', 'title', 'ectsCredits']);
        $exporterConfiguration->setFields(
            ['uid', 'title', 'ectsCredits', 'academicDegree.graduation.title', 'courseLanguages.language']
        );

        $exportService = $this->objectManager->get(ExportService::class);

        $exportService->setExporter(new CsvExportProvider);
        $exportService->setExportRecords($studyCourses);
        $exportService->setExportConfiguration($exporterConfiguration);

        $exportService->export();
    }

    /**
     * @param StudyCourse $studyCourse
     */
    protected function getPossibleFieldsToExportForStudyCourses($studyCourse)
    {
        $reflectionService = $this->objectManager->get(ReflectionService::class);
        $studyCoursePropertyList = $reflectionService->getClassPropertyNames(get_class($studyCourse));
        $propertyArray = [];

        /* 1. Select Exporter
         * 2. Select StudyCourses
         *   2.1 List StudyCourses
         *    2.1.1 Only Manuel
         *    2.1.2 Later with special filters?
         *
         * 3. Select Fields for Export
         *
         * 4. Overview
         */

        foreach ($studyCoursePropertyList as $propertyName) {
            $property = $studyCourse->_getProperty($propertyName);
            if ($property instanceof ObjectStorage) {
                $propertyArray[$propertyName]['type'] = ObjectStorage::class;
            }
            if ($property instanceof AbstractDomainObject) {
                $propertyArray[$propertyName]['type'] = AbstractDomainObject::class;
            }
            if (gettype($property) !== 'object' && gettype($property) !== 'NULL') {
                $propertyArray[$propertyName]['type'] = gettype($property);
            }

            $propertyArray[$propertyName]['property'] = $property;
        }
    }
}
