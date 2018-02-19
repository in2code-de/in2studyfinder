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

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;
use In2code\In2studyfinder\Service\ExportService;
use In2code\In2studyfinder\Utility\FileUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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

    public function initializeAction()
    {
        parent::initializeAction();

        $this->reflectionService = $this->objectManager->get(ReflectionService::class);
    }

    public function listAction()
    {
        $this->validateSettings();

        $studyCourses = $this->getStudyCourseRepository()->findAll();
        $possibleExportDataProvider = $this->getPossibleExportDataProvider();
        $propertyArray = [];

        if ($studyCourses->getFirst() !== null) {
            $this->getFullPropertyList(
                $propertyArray,
                $this->reflectionService->getClassSchema($studyCourses->getFirst())->getProperties()
            );
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noCourses.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noCourses.title', 'in2studyfinder'),
                AbstractMessage::WARNING
            );
        }

        $itemsPerPage = $this->settings['pagination']['itemsPerPage'];

        if ($this->request->hasArgument('itemsPerPage')) {
            $itemsPerPage = $this->request->getArgument('itemsPerPage');
        }


        $this->view->assignMultiple(
            [
                'studyCourses' => $studyCourses->toArray(),
                'exportDataProvider' => $possibleExportDataProvider,
                'availableFieldsForExport' => $propertyArray,
                'itemsPerPage' => $itemsPerPage
            ]
        );
    }

    public function getPossibleExportDataProvider()
    {
        $possibleDataProvider = [];

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes'])
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes'] as $providerName => $providerClass) {
                if (!class_exists($providerClass)) {
                    $this->addFlashMessage(
                        'export provider class "' . $providerClass . '" was not found',
                        'export provider class not found',
                        AbstractMessage::ERROR
                    );
                } else {
                    $possibleDataProvider[$providerName] = $providerClass;
                }
            }

        }

        return $possibleDataProvider;
    }

    /**
     * @param string $exporter
     * @param array $selectedProperties
     * @param array $courseList
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function exportAction($exporter, $selectedProperties, $courseList)
    {
        if (empty($selectedProperties) || empty($courseList)) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.title', 'in2studyfinder'),
                AbstractMessage::ERROR
            );

            $this->forward('list');
        }

        $courses = $this->studyCourseRepository->getCoursesWithUidIn($courseList)->toArray();

        /** @var ExportInterface $exportType */
        $exportType = $this->objectManager->get($exporter);
        $exportConfiguration = $this->objectManager->get(ExportConfiguration::class);
        $exportConfiguration
            ->setPropertiesToExport($selectedProperties)
            ->setExporter($exportType);

        $exportService =  $this->objectManager->get(ExportService::class, $exportConfiguration);
        $processedRecords = $exportService->prepareRecordsForExport($courses);
        $exportConfiguration->setRecordsToExport($processedRecords);

        $filenameAndPath = $exportService->export();
        $filename = FileUtility::getFilenameFromFileWithPath($filenameAndPath);

        $headers = array(
            'Pragma'                    => 'public',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition'       => 'attachment; filename="'. $filename .'"',
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => 'text/plain',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length'            => filesize($filenameAndPath)
        );

        foreach($headers as $header => $data)
            $this->response->setHeader($header, $data);

        $this->response->sendHeaders();

        @readfile($filenameAndPath);
        exit;
    }

    protected function getFullPropertyList(&$propertyArray, $objectProperties)
    {

        foreach ($objectProperties as $propertyName => $propertyInformation) {
            if (!in_array($propertyName, $this->settings['backend']['excludedPropertiesForExport'])) {
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

    protected function validateSettings()
    {
        if (!isset($this->settings['storagePid']) || empty($this->settings['storagePid'])) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noStoragePid.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noStoragePid.title', 'in2studyfinder'),
                AbstractMessage::ERROR
            );
        }

        if (!isset($this->settings['settingsPid']) || empty($this->settings['settingsPid'])) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noSettingsPid.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noSettingsPid.title', 'in2studyfinder'),
                AbstractMessage::ERROR
            );
        }
    }
}
