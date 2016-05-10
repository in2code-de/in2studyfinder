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

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * StudyCourseController
 */
class StudyCourseController extends AbstractController
{

    /**
     * @var array
     */
    protected $filterTypeRepositories = [];

    /**
     * @var array
     */
    protected $filterTypes = [];

    /**
     * @var \In2code\In2studyfinder\Utility\SessionUtility
     * @inject
     */
    protected $sessionUtility;

    /**
     * @var array
     */
    protected $allowedSearchFields = [];

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->settings = ExtensionUtility::getExtensionConfiguration('in2studyfinder');

        foreach ($this->settings['filter']['filterTypeLabelField'] as $key => $value) {
            $this->allowedSearchFields[] = $key;
        }

        $this->setFilterTypesAndRepositories();

    }

    /**
     *
     */
    protected function setFilterTypesAndRepositories()
    {
        foreach ($this->settings['filter']['allowedFilterTypes'] as $filterType) {

            $this->filterTypeRepositories[$filterType] = $this->objectManager->get(
                $filterType . 'Repository'
            );

            $filterTypeTitle = substr(
                $filterType,
                strripos($filterType, '\\') + 1
            );

            $this->filterTypes[lcfirst($filterTypeTitle)] = $this->filterTypeRepositories[$filterType]->findAll();
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $this->assignAllStudyCourses();
    }

    protected function assignAllStudyCourses()
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $studyCourses */
        $studyCourses = $this->studyCourseRepository->findAll();

        $this->view->assign('filterTypes', $this->filterTypes);
        $this->view->assign('availableFilterOptions', $this->getAvailableFilterOptionsFromQueryResult($studyCourses));
        $this->view->assign('studyCourseCount', count($studyCourses));
        $this->view->assign('studyCourses', $studyCourses);
        $studyCoursesSortedByLettersArray = $this->getStudyCoursesLetterArray($studyCourses);
        $this->view->assign('studyCoursesLetterArray', $studyCoursesSortedByLettersArray);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $studyCourses
     * @return array
     */
    protected function getStudyCoursesLetterArray($studyCourses)
    {
        $studyCoursesSortedByLettersArray = [];

        foreach ($studyCourses as $studyCourse) {
            $firstLetter = substr($studyCourse->getTitle(), 0, 1);
            $studyCoursesSortedByLettersArray[$firstLetter][] = $studyCourse;
        }

        return $studyCoursesSortedByLettersArray;
    }

    /**
     * detail show
     *
     * @return void
     */
    public function detailAction()
    {
        $getData = GeneralUtility::_GET('tx_in2studyfinder_studycourse');

        if ($getData['studyCourse'] && $getData['studyCourse'] !== '') {
            $studyCourse = $this->studyCourseRepository->findByUid($getData['studyCourse']);
            $this->view->assign('studyCourse', $studyCourse);
        } else {
            $this->redirect('list');
        }
    }

    /**
     * @return void
     */
    public function initializeFilterAction()
    {

        if ($this->request->hasArgument('searchOptions')) {
            // filter empty options
            $sanitizedSearchOptions = array_filter($this->request->getArgument('searchOptions'));
            // remove not allowed keys (prevents SQL Injection, too)
            foreach (array_keys($sanitizedSearchOptions) as $studyCoursePropertyName) {
                if (!in_array($studyCoursePropertyName, $this->allowedSearchFields)) {
                    unset($sanitizedSearchOptions[$studyCoursePropertyName]);
                }
            }
            $this->request->setArgument('searchOptions', $sanitizedSearchOptions);
        }
    }

    /**
     * @param array $searchOptions
     * @return void
     */
    public function filterAction($searchOptions = array())
    {
        if (empty($searchOptions) && $this->request->getMethod() === 'GET') {
            if ($this->sessionUtility->has('searchOptions')) {
                $searchOptions = $this->sessionUtility->get('searchOptions');
            }
        }
        if (!empty($searchOptions)) {
            $this->view->assign('searchedOptions', $searchOptions);

            $foundStudyCourses = $this->processSearch($searchOptions);

            $this->view->assign('studyCoursesLetterArray', $this->getStudyCoursesLetterArray($foundStudyCourses));
            $this->view->assign(
                'availableFilterOptions', $this->getAvailableFilterOptionsFromQueryResult($foundStudyCourses)
            );
            $this->view->assign('studyCourseCount', count($foundStudyCourses));
            $this->view->assign('filterTypes', $this->filterTypes);
            $this->view->assign('studyCourses', $foundStudyCourses);
            $this->sessionUtility->set('searchOptions', $searchOptions);
        } else {
            $this->assignAllStudyCourses();
        }
    }

    /**
     * @param $searchOptions
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    protected function processSearch($searchOptions)
    {
        return $this->studyCourseRepository->findAllFilteredByOptions($searchOptions);
    }

    /**
     * @param QueryResult $queryResult
     * @return array
     * @throws \TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException
     */
    protected function getAvailableFilterOptionsFromQueryResult($queryResult)
    {
        $availableOptions = array();
        foreach ($queryResult as $studyCourse) {
            $properties = $studyCourse->_getProperties();

            foreach ($properties as $property) {
                try {
                    if (is_object($property)) {

                        $fullQualifiedClassName = get_class($property);
                        $propertyName = lcfirst(
                            substr(
                                $fullQualifiedClassName,
                                strripos($fullQualifiedClassName, "\\") + 1
                            )
                        );

                        if ($property instanceof ObjectStorage) {
                            foreach ($property as $groupedProperty) {
                                if ($groupedProperty instanceof AbstractDomainObject) {
                                    $fullQualifiedClassName = get_class($groupedProperty);
                                    $propertyName = lcfirst(
                                        substr(
                                            $fullQualifiedClassName,
                                            strripos($fullQualifiedClassName, "\\") + 1
                                        )
                                    );

                                    if ($propertyName !== 'TtContent') {
                                        $availableOptions[$propertyName][] = $groupedProperty->getUid();
                                    }
                                }
                            }
                        } elseif ($property instanceof AbstractDomainObject) {
                            $availableOptions[$propertyName][] = $property->getUid();
                        }
                    }
                    $availableOptions[$propertyName] = array_unique($availableOptions[$propertyName]);
                } catch (\Exception $e) {
                    GeneralUtility::devLog(
                        'Unexpected exception thrown in "' . __METHOD__ . '". ',
                        'in2studyfinder',
                        AbstractMessage::ERROR,
                        array(
                            'Exception' => $e,
                            'studyCourse' => $studyCourse,
                            'propertyName' => $propertyName,
                        )
                    );
                }
            }
        }
        return $availableOptions;
    }
}
