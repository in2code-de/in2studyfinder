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
use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Property\Exception;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StudyCourseController extends ActionController
{
    /**
     * studyCourseRepository
     *
     * @var \In2code\In2studyfinder\Domain\Repository\StudyCourseRepository
     */
    protected $studyCourseRepository = null;

    /**
     * @var array
     */
    protected $filterTypeRepositories = [];

    /**
     * @var array
     */
    protected $filterTypes = [];

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

        if (ExtensionUtility::isIn2studycoursesExtendLoaded()) {
            $this->studyCourseRepository =
                $this->objectManager->get('In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseRepository');
        }

        $this->setFilterTypesAndRepositories();
    }

    /**
     * Inject Study Course Repository
     *
     * @param StudyCourseRepository $studyCourseRepository
     */
    public function injectStudyCourseRepository(StudyCourseRepository $studyCourseRepository)
    {
        $this->studyCourseRepository = $studyCourseRepository;
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
    public function filterAction($searchOptions = [])
    {
        if (!empty($searchOptions)) {
            $foundStudyCourses = $this->processSearch($searchOptions);

            $studyCourses = $this->sortStudyCourses($foundStudyCourses);

            $this->view->assignMultiple(
                [
                    'searchedOptions' => $searchOptions,
                    'availableFilterOptions' => $this->getAvailableFilterOptionsFromQueryResult($studyCourses),
                    'studyCourseCount' => count($foundStudyCourses),
                    'filterTypes' => $this->filterTypes,
                    'studyCourses' => $foundStudyCourses,
                ]
            );
        } else {
            $this->assignStudyCourses();
        }
    }

    /**
     * detail show
     *
     * @param StudyCourse $studyCourse
     * @return void
     */
    public function detailAction(StudyCourse $studyCourse = null)
    {
        if ($studyCourse) {
            $this->writePageMetadata($studyCourse);
            $this->view->assign('studyCourse', $studyCourse);

        } else {
            $this->redirect('listAction', null, null, null, $this->settings['flexform']['studyCourseListPage']);
        }
    }

    /**
     * call page not found if the request throws an exception
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \Exception|Exception
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response)
    {
        try {
            parent::processRequest($request, $response);
        } catch (Exception $exception) {
            if ($exception instanceof Exception) {
                $GLOBALS['TSFE']->pageNotFoundAndExit();
            }
            throw $exception;
        }
    }

    /**
     * get the defaultQuerySettings for filters
     *
     * @return QuerySettingsInterface
     */
    protected function getDefaultQuerySettings()
    {
        /** @var QuerySettingsInterface $defaultQuerySettings */
        $defaultQuerySettings = $this->objectManager->get(QuerySettingsInterface::class);

        $defaultQuerySettings->setStoragePageIds(
            [
                $this->settings['settingsPid']
            ]
        );

        $defaultQuerySettings->setLanguageOverlayMode(true);
        $defaultQuerySettings->setLanguageMode('strict');

        return $defaultQuerySettings;
    }

    /**
     * set the Models and the Repositories
     *
     * @return void
     */
    protected function setFilterTypesAndRepositories()
    {

        foreach ($this->settings['filter']['allowedFilterTypes'] as $filterType) {
            $repository = ClassNamingUtility::translateModelNameToRepositoryName($filterType);

            if (class_exists($repository)) {
                $this->filterTypeRepositories[$filterType] = $this->objectManager->get($repository);

                $filterTypeTitle = substr($filterType, strripos($filterType, '\\') + 1);

                $this->filterTypeRepositories[$filterType]->setDefaultQuerySettings($this->getDefaultQuerySettings());

                $this->filterTypes[lcfirst($filterTypeTitle)] = $this->filterTypeRepositories[$filterType]->findAll();
            } else {
                $this->filterTypes[lcfirst($filterType)] = ['isSet', 'isUnset'];
            }
        }
    }

    /**
     * assign StudyCourses to the view
     *
     * @return void
     */
    protected function assignStudyCourses()
    {
        $studyCourses = $this->getStudyCourses();

        $this->view->assignMultiple(
            [
                'filterTypes' => $this->filterTypes,
                'availableFilterOptions' => $this->getAvailableFilterOptionsFromQueryResult($studyCourses),
                'studyCourseCount' => count($studyCourses),
                'studyCourses' => $studyCourses,
            ]
        );
    }

    /**
     * @return array
     */
    protected function getStudyCourses()
    {

        $selectedOptions = $this->getSelectedFlexformOptions();

        if (!empty($selectedOptions)) {
            $studyCourses = $this->processSearch($selectedOptions);
        } else {
            $studyCourses = $this->studyCourseRepository->findAll();
        }

        $studyCourses = $this->sortStudyCourses($studyCourses);

        return $studyCourses;
    }

    /**
     * @return array
     */
    protected function getSelectedFlexformOptions()
    {
        $selectedOptions = [];

        if (!empty($this->settings['flexform']['select'])) {
            foreach ($this->settings['flexform']['select'] as $filterType => $uid) {
                if ($uid !== '') {
                    $selectedOptions[$filterType] = GeneralUtility::intExplode(',', $uid, true);
                }
            }
        }

        return $selectedOptions;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     *
     * @param StudyCourse $studyCourse
     *
     * @return void
     */
    protected function writePageMetadata($studyCourse)
    {

        if (!empty($studyCourse->getMetaPagetitle())) {
            $GLOBALS['TSFE']->page['title'] = $studyCourse->getMetaPagetitle();
        } else {
            $GLOBALS['TSFE']->page['title'] =
                $studyCourse->getTitle() . ' - ' . $studyCourse->getAcademicDegree()->getDegree();
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
     * @param $searchOptions
     * @return QueryResultInterface
     */
    protected function processSearch($searchOptions)
    {
        return $this->studyCourseRepository->findAllFilteredByOptions($searchOptions);
    }

    /**
     * @param QueryResultInterface $studyCourses
     *
     * @return array
     */
    protected function sortStudyCourses(QueryResultInterface $studyCourses)
    {
        $studyCoursesArray = (array)$studyCourses->toArray();

        /* sort the Studycourses with usort see: Domain/Model/StudyCourse:cmpObj */
        usort($studyCoursesArray, array(StudyCourse::class, "cmpObj"));

        return $studyCoursesArray;
    }

    /**
     * @param array $studyCourses
     * @return array
     * @throws \TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException
     */
    protected function getAvailableFilterOptionsFromQueryResult($studyCourses)
    {
        $availableOptions = [];
        foreach ($studyCourses as $studyCourse) {
            $properties = $studyCourse->_getProperties();

            $this->getAvailableFilterOptionsFromProperties($properties, $availableOptions);
        }

        return $availableOptions;
    }

    /**
     * @param array $propertyArray
     * @param array $availableOptionArray
     * @param int $currentLevel
     *
     * @return void
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
                            $property->_getProperties(),
                            $availableOptionArray,
                            $currentLevel + 1
                        );

                        $className = ExtensionUtility::getClassName($property);

                        if ($className !== 'ttContent') {
                            $availableOptionArray[$className][] = $property->getUid();
                        }
                    }
                } else {
                    if (array_key_exists($name, $this->filterTypes)) {
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
