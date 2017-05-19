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
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
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
    protected $filters = [];

    /**
     * cacheUtility
     *
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cacheInstance;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $cObj;

    /**
     * initialize action
     */
    protected function initializeAction()
    {
        if (ConfigurationUtility::isCachingEnabled()) {
            $this->cacheInstance = GeneralUtility::makeInstance(CacheManager::class)->getCache('in2studyfinder');
            $this->cObj = $this->configurationManager->getContentObject();
        }

        if (ExtensionUtility::isIn2studycoursesExtendLoaded()) {
            $this->studyCourseRepository =
                $this->objectManager->get('In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseRepository');
        }

        $this->setFilters();
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
            $sanitizedSearchOptions = array_filter((array)$this->request->getArgument('searchOptions'));

            // remove not allowed keys (prevents SQL Injection, too)
//            foreach (array_keys($sanitizedSearchOptions) as $studyCoursePropertyName) {
//                if (!in_array($studyCoursePropertyName, $this->allowedSearchFields)) {
//                    unset($sanitizedSearchOptions[$studyCoursePropertyName]);
//                }
//            }
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
            if (ConfigurationUtility::isCachingEnabled()) {
                $cacheIdentifier = md5(
                    $GLOBALS['TSFE']->id . '-' . $this->cObj->data['uid'] . '-' . $GLOBALS['TSFE']->sys_language_uid . '-' . $this->actionMethodName . '-' . json_encode(
                        $searchOptions
                    )
                );

                $foundStudyCourses = $this->cacheInstance->get($cacheIdentifier);

                if (!$foundStudyCourses) {
                    $foundStudyCourses = $this->processSearch($searchOptions);
                    $this->cacheInstance->set($cacheIdentifier, $foundStudyCourses, ['in2studyfinder']);
                }
            } else {
                $foundStudyCourses = $this->processSearch($searchOptions);
            }

            $studyCourses = $this->sortStudyCourses($foundStudyCourses->toArray());

            $this->view->assignMultiple(
                [
                    'searchedOptions' => $searchOptions,
                    'availableFilterOptions' => $this->getAvailableFilterOptionsFromQueryResult($foundStudyCourses),
                    'studyCourseCount' => count($foundStudyCourses),
                    'filters' => $this->filters,
                    'studyCourses' => $studyCourses,
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

    protected function setFilters()
    {
        foreach ($this->settings['filters'] as $filterName => $filterProperties) {
            if ($filterProperties['type']) {
                $this->filters[$filterName]['type'] = $filterProperties['type'];

                $this->filters[$filterName]['coursePropertyName'] = $filterProperties['coursePropertyName'];

                switch ($filterProperties['type']) {
                    case 'object':
                        $fullQualifiedRepositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName(
                            $filterProperties['filterModelClass']
                        );

                        if (class_exists($fullQualifiedRepositoryClassName)) {
                            $repository = $this->objectManager->get($fullQualifiedRepositoryClassName);
                            $repository->setDefaultQuerySettings($this->getDefaultQuerySettings());

                            $this->filters[$filterName]['repository'] = $repository;
                            $this->filters[$filterName]['filterOptions'] = $repository->findAll();
                        }
                        break;
                    case 'boolean':
                        $this->filters[$filterName]['filterOptions'] = [true, false];
                        break;
                    default:
                        break;
                }
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
                'filters' => $this->filters,
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

        if (ConfigurationUtility::isCachingEnabled()) {
            if (!empty($selectedOptions)) {
                $cacheIdentifier = md5(
                    $GLOBALS['TSFE']->id . "-" . $this->cObj->data['uid'] . "-" . $GLOBALS['TSFE']->sys_language_uid . "-" . $this->actionMethodName . '-' . json_encode(
                        $selectedOptions
                    )
                );
            } else {
                $cacheIdentifier = md5(
                    $GLOBALS['TSFE']->id . "-" . $this->cObj->data['uid'] . "-" . $GLOBALS['TSFE']->sys_language_uid . "-" . $this->actionMethodName
                );
            }

            $studyCourses = $this->cacheInstance->get($cacheIdentifier);

            if (!$studyCourses) {
                if (!empty($selectedOptions)) {
                    $studyCourses = $this->processSearch($selectedOptions);
                } else {
                    $studyCourses = $this->studyCourseRepository->findAll();
                }
                // In diesem Beispiel wird das Ergebnis des Repositories im Cache gespeichert.
                // Es ist natürlich möglich noch viel mehr zu speichern.
                $this->cacheInstance->set($cacheIdentifier, $studyCourses, ['in2studyfinder']);
            }
        } else {
            if (!empty($selectedOptions)) {
                $studyCourses = $this->processSearch($selectedOptions);
            } else {
                $studyCourses = $this->studyCourseRepository->findAll();
            }
        }

        $studyCourses = $this->sortStudyCourses($studyCourses->toArray());

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
        $mergedOptions = [];

        // merge filter options to searchedOptions
        foreach ($searchOptions as $filterName => $searchedOptions) {
            $mergedOptions[$this->filters[$filterName]['coursePropertyName']] = $searchOptions[$filterName];
        }

        return $this->studyCourseRepository->findAllFilteredByOptions($mergedOptions);
    }

    /**
     * @param array $studyCourses
     *
     * @return array
     */
    protected function sortStudyCourses($studyCourses)
    {
        /* sort the Studycourses with usort see: Domain/Model/StudyCourse:cmpObj */
        usort($studyCourses, array(StudyCourse::class, "cmpObj"));

        return $studyCourses;
    }

    /**
     * @param array $studyCourses
     * @return array
     * @throws \TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException
     */
    protected function getAvailableFilterOptionsFromQueryResult($studyCourses)
    {
        $availableOptions = [];

        foreach ($this->filters as $filterName => $filter) {
            /** @var $course StudyCourse */
            foreach ($studyCourses as $course) {
                $property = $this->getPropertyByPropertyPath($course, $filter['coursePropertyName']);

                switch ($filter['type']) {
                    case 'object':
                        if ($property instanceof ObjectStorage) {
                            foreach ($property as $obj) {
                                $availableOptions[$filterName][$obj->getUid()] = $obj->getUid();
                            }
                        } else {
                            if ($property instanceof AbstractDomainObject) {
                                $availableOptions[$filterName][$property->getUid()] = $property->getUid();
                            } else {
                                // Throw not Supported Object
                            }
                        }
                        break;
                    case 'boolean':
                        if ($property !== '' && $property !== 0 && $property !== false) {
                            $availableOptions[$filterName][0] = 'true';
                        } else {
                            $availableOptions[$filterName][0] = 'false';
                        }
                        break;
                    default:
                        // Throw not Supported Filter Type
                        break;
                }
            }
        }

        return $availableOptions;
    }

    /**
     * get the property of an object or an sub object by an given path name
     * e.g academicDegree/graduation
     *
     * @param AbstractDomainObject $obj
     * @param string $propertyPath
     * @return mixed
     */
    protected function getPropertyByPropertyPath(AbstractDomainObject $obj, $propertyPath)
    {
        if (strpos($propertyPath, '.')) {
            $pathSegments = GeneralUtility::trimExplode('.', $propertyPath);
            $property = null;
            foreach ($pathSegments as $pathSegment) {
                if ($property === null) {
                    $property = $obj->_getProperty($pathSegment);
                } else {
                    $property = $property->_getProperty($pathSegment);
                }
            }
        } else {
            $property = $obj->_getProperty($propertyPath);
        }

        return $property;
    }
}
