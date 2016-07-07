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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * initialize action
     */
    protected function initializeAction()
    {
        foreach ($this->settings['filter']['filterTypeLabelField'] as $key => $value) {
            $this->allowedSearchFields[] = $key;
        }

        $this->setFilterTypesAndRepositories();

        $extendedStudyCourseClassName = 'In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseRepository';

        if (ExtensionUtility::isIn2studycoursesExtendLoaded()
            && class_exists(
                $extendedStudyCourseClassName

            )
        ) {
            $this->studyCourseRepository = $this->objectManager->get(
                $extendedStudyCourseClassName
            );
        }
    }

    /**
     * set the Models and the Repositories
     */
    protected function setFilterTypesAndRepositories()
    {

        foreach ($this->settings['filter']['allowedFilterTypes'] as $filterType) {

            if (class_exists($filterType . 'Repository')) {
                $this->filterTypeRepositories[$filterType] = $this->objectManager->get(
                    $filterType . 'Repository'
                );

                $filterTypeTitle = substr(
                    $filterType,
                    strripos($filterType, '\\') + 1
                );

                $this->filterTypes[lcfirst($filterTypeTitle)] = $this->filterTypeRepositories[$filterType]->findAll();
            } else {
                $this->filterTypes[lcfirst($filterType)] = ['isSet', 'isUnset'];
            }


        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $this->assignStudyCourses();

    }

    /**
     * assign StudyCourses to the view
     */
    protected function assignStudyCourses()
    {
        $studyCourses = $this->getStudyCourses();

        $this->view->assign('filterTypes', $this->filterTypes);
        $this->view->assign('availableFilterOptions', $this->getAvailableFilterOptionsFromQueryResult($studyCourses));
        $this->view->assign('studyCourseCount', count($studyCourses));
        $this->view->assign('studyCourses', $studyCourses);
        $studyCoursesSortedByLettersArray = $this->getStudyCoursesLetterArray($studyCourses);
        $this->view->assign('studyCoursesLetterArray', $studyCoursesSortedByLettersArray);
    }

    /**
     * @return QueryResult
     */
    protected function getStudyCourses()
    {
        $options = null;
        foreach ($this->settings['flexform']['select'] as $filterType => $uid) {
            if ($uid !== '') {
                $options[$filterType] = GeneralUtility::intExplode(',', $uid, true);
            }
        }

        if ($options !== null) {
            $studyCourses = $this->processSearch($options);
        } else {
            $studyCourses = $this->studyCourseRepository->findAll();
        }

        return $studyCourses;
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

        $getData = GeneralUtility::_GET('tx_in2studyfinder_pi2');

        if (GeneralUtility::_POST('tx_in2studyfinder_studycourse')) {
            $getData['studyCourse'] = GeneralUtility::_POST('tx_in2studyfinder_studycourse')['studyCourse'];
        }

        if ($getData['studyCourse'] && $getData['studyCourse'] !== '') {
            $studyCourse = $this->studyCourseRepository->findByUid($getData['studyCourse']);

            $this->writePageMetadata($studyCourse);

            $this->view->assign('studyCourse', $studyCourse);
        } else {
            $this->redirect('list');
        }
    }

    /**
     * @param $studyCourse
     */
    protected function writePageMetadata($studyCourse)
    {

        if (!empty($studyCourse->getMetaPagetitle())) {
            $GLOBALS['TSFE']->page['title'] = $studyCourse->getMetaPagetitle();
        } else {
            $GLOBALS['TSFE']->page['title'] = $studyCourse->getTitle() . ' - ' . $studyCourse->getAcademicDegree(
                )->getDegree();
        }
        if (!empty($studyCourse->getMetaDescription())) {
            $metaDescription = '<meta name="description" content="' . $studyCourse->getMetaDescription() . '">';
            $this->response->addAdditionalHeaderData($metaDescription);
        }
        if (!empty($studyCourse->getMetaKeywords())) {
            $metaKeywords = '<meta name="keywords" content="' . $studyCourse->getMetaKeywords() . '">';
            $this->response->addAdditionalHeaderData($metaKeywords);
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
            $this->assignStudyCourses();
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

            $this->getAvailableFilterOptionsFromProperties(
                $properties, $availableOptions
            );
        }

        return $availableOptions;
    }

    /**
     * @param array $propertyArray
     * @param array $availableOptionArray
     * @param int $currentLevel
     */
    protected function getAvailableFilterOptionsFromProperties(
        $propertyArray,
        &$availableOptionArray,
        $currentLevel = 0
    ) {
        if ($currentLevel < $this->settings['filter']['recursive']) {
            foreach ($propertyArray as $name => $property) {
                if (is_object($property)) {
                    if ($property instanceof ObjectStorage) {
                        $this->getAvailableFilterOptionsFromProperties($property, $availableOptionArray, $currentLevel);
                    } elseif ($property instanceof AbstractDomainObject) {

                        $this->getAvailableFilterOptionsFromProperties(
                            $property->_getProperties(), $availableOptionArray, $currentLevel + 1
                        );

                        $className = ExtensionUtility::getClassName($property);

                        if ($className !== 'ttContent') {
                            $availableOptionArray[$className][] = $property->getUid();
                        }
                    }
                } else {
                    if (array_key_exists($name,$this->filterTypes)) {
                        if ($property !== '' && $property !== 0 && $property !== false) {
                            $availableOptionArray[$name][] = 'isSet';
                        } else {
                            $availableOptionArray[$name][] = 'isUnset';
                        }
                    }
                }
            }
        }
    }

}
