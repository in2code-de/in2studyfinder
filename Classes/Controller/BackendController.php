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

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Service\ExportService;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
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

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function listAction()
    {
        $this->validateSettings();

        $recordLanguage = 0;

        if ($this->request->hasArgument('recordLanguage')) {
            $recordLanguage = (int)$this->request->getArgument('recordLanguage');
        }

        $studyCourses = $this->getStudyCoursesForExportList($recordLanguage);

        $possibleExportDataProvider = $this->getPossibleExportDataProvider();
        $propertyArray = [];

        if ($studyCourses !== null && empty($studyCourses)) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noCourses.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noCourses.title', 'in2studyfinder'),
                AbstractMessage::WARNING
            );
        } else {
            $this->getFullPropertyList(
                $propertyArray,
                $this->reflectionService->getClassSchema(
                    $this->getStudyCourseRepository()->findOneByDeleted(0)
                )->getProperties()
            );
        }

        $itemsPerPage = $this->settings['pagination']['itemsPerPage'];

        if ($this->request->hasArgument('itemsPerPage')) {
            $itemsPerPage = $this->request->getArgument('itemsPerPage');
        }

        $this->view->assignMultiple(
            [
                'studyCourses' => $studyCourses,
                'exportDataProvider' => $possibleExportDataProvider,
                'availableFieldsForExport' => $propertyArray,
                'sysLanguages' => $this->getSysLanguages(),
                'itemsPerPage' => $itemsPerPage
            ]
        );
    }

    /**
     * @return array
     */
    protected function getSysLanguages()
    {
        $sysLanguages = [
            0 => 'default'
        ];

        $queryBuilder = $this->objectManager->get(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $languageRecords = $queryBuilder
            ->select('*')
            ->from('sys_language')
            ->where($queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)))
            ->orderBy('sorting')
            ->execute()->fetchAll();

        foreach ($languageRecords as $languageRecord) {
            $sysLanguages[(int)$languageRecord['uid']] =
                LocalizationUtility::translate(
                    'LLL:EXT:core/Resources/Private/Language/db.xlf:sys_language.language_isocode.' .
                    $languageRecord['language_isocode']
                );
        }

        return $sysLanguages;
    }

    /**
     * @param int $languageUid
     * @return array|null
     */
    protected function getStudyCoursesForExportList($languageUid = 0)
    {
        $queryBuilder = $this->objectManager->get(ConnectionPool::class)->getQueryBuilderForTable(StudyCourse::TABLE);
        $includeDeleted = (int)$this->settings['backend']['export']['includeDeleted'];
        $includeHidden = (int)$this->settings['backend']['export']['includeHidden'];
        $storagePid = (int)$this->settings['storagePid'];

        if ($includeDeleted) {
            $queryBuilder->getRestrictions()->removeByType(DeletedRestriction::class);
        }

        if ($includeHidden) {
            $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        }

        $records = $queryBuilder
            ->select('uid', 'l10n_parent', 'title', 'hidden', 'deleted')
            ->from(StudyCourse::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($languageUid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($storagePid, \PDO::PARAM_INT))
            )
            ->orderBy('title')
            ->execute()->fetchAll();

        return $records;
    }

    /**
     * @return array
     */
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
     * @param integer $recordLanguage
     * @param array $selectedProperties
     * @param array $courseList
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function exportAction($exporter, $recordLanguage, $selectedProperties, $courseList)
    {
        if (empty($selectedProperties) || empty($courseList)) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.title', 'in2studyfinder'),
                AbstractMessage::ERROR
            );

            $this->forward('list');
        }

        /**
         * WORKAROUND
         *
         * see @getCoursesForExport
         */
        $courses = $this->getCoursesForExport($courseList, $recordLanguage);

        $exportService = $this->objectManager->get(ExportService::class, $exporter, $selectedProperties, $courses);

        $exportService->export();
    }

    /**
     * WORKAROUND
     *
     * we make an frontend request because returns the attached records of the course always in the default language.
     * This function will be removed if i found a other solution.
     *
     * @param array $courseList
     * @param int $recordLanguage
     *
     * @return array
     * @throws Exception
     */
    protected function getCoursesForExport(array $courseList, $recordLanguage)
    {
        $content['tx_in2studyfinder_pi1']['courseList'] = $courseList;

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($content)
            ],
        ];

        $context = stream_context_create($opts);

        $result = $this->executeFrontendRequest($this->request->getBaseUri(), $recordLanguage, $context);

        return unserialize(json_decode($result));
    }

    /**
     * @param $url
     * @param $recordLanguage
     * @param $context
     * @return bool|string
     * @throws Exception
     */
    protected function executeFrontendRequest($url, $recordLanguage, $context)
    {
        $urlParts = parse_url($url);
        $result = false;

        if ($urlParts['scheme'] === 'https') {
            $result = file_get_contents(
                $urlParts['scheme'] . '://' . $urlParts['host'] . '/?type=2308171056&L=' . $recordLanguage,
                false,
                $context
            );
        }


        // if the scheme is http or the request returning an error with https
        if ($result === false || $urlParts['scheme'] === 'http') {
            $result = file_get_contents(
                'http://' . $urlParts['host'] . '/?type=2308171056&L=' . $recordLanguage,
                false,
                $context
            );
        }

        if ($result === false) {
            throw new Exception('Error at frontend request of selected programs', 1547629143);
        }

        return $result;
    }

    /**
     * @param $propertyArray
     * @param $objectProperties
     */
    protected function getFullPropertyList(
        &$propertyArray,
        $objectProperties
    ) {

        foreach ($objectProperties as $propertyName => $propertyInformation) {
            if (!in_array($propertyName, $this->settings['backend']['export']['excludedPropertiesForExport'])) {
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

    /**
     * @return void
     */
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
