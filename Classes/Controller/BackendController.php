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
use In2code\In2studyfinder\DataProvider\ExportProviderInterface;
use In2code\In2studyfinder\Domain\Service\ExportService;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class BackendController extends AbstractController
{
    /**
     * @var ReflectionService
     */
    protected $reflectionService;

    protected $excludedProperties = [
        '_localizedUid',
        '_languageUid',
        '_versionedUid',
        'globalDataPreset',
        'globalData',
        'contentElements',
        'pid',
        'uid'
    ];

    public function initializeAction()
    {
        parent::initializeAction();

        $this->reflectionService = $this->objectManager->get(ReflectionService::class);

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

        $propertyArray = [];
        $this->getFullPropertyList(
            $propertyArray,
            $this->reflectionService->getClassSchema($studyCourses[0])->getProperties()
        );

        $this->view->assignMultiple(
            [
                'studyCourses' => $studyCourses,
                'exportDataProvider' => $possibleExportDataProvider,
                'availableFieldsForExport' => $propertyArray
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

    public function exportAction($exportClass, $studyCourses, $exportFields)
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
        $exporterConfiguration->setFields($exportFields);

        $exportService = $this->objectManager->get(ExportService::class);

        /** @var ExportProviderInterface $exporter */
        $exporter = $this->objectManager->get($exportClass);

        $exportService->setExporter($exporter);
        $exportService->setExportRecords($studyCourses);
        $exportService->setExportConfiguration($exporterConfiguration);

        $exportService->export();
    }

    protected function getFullPropertyList(&$propertyArray, $objectProperties)
    {
        foreach ($objectProperties as $propertyName => $propertyInformation) {
            if (!in_array($propertyName, $this->excludedProperties)) {
                if ($propertyInformation['type'] === ObjectStorage::class) {
                    if (class_exists($propertyInformation['elementType'])) {
                        $this->getFullPropertyList(
                            $propertyArray[$propertyName],
                            $this->reflectionService->getClassSchema($propertyInformation['elementType'])
                                ->getProperties()
                        );
                    }
                } else {
                    if (class_exists($propertyInformation['type'])) {
                        $this->getFullPropertyList(
                            $propertyArray[$propertyName],
                            $this->reflectionService->getClassSchema($propertyInformation['type'])->getProperties()
                        );
                    } else {
                        $propertyArray[$propertyName] = $propertyName;
                    }
                }
            }
        }
    }


}
