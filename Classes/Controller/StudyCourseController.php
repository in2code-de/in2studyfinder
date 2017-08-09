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
use In2code\In2studyfinder\Domain\Repository\StudyCourseListContextRepository;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Property\Exception;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StudyCourseController extends ActionController
{
    /**
     * @var \In2code\In2studyfinder\Domain\Repository\StudyCourseListContextRepository
     */
    protected $studyCourseListContextRepository = null;

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
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Response
     */
    protected $response;

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
            if (class_exists('\\In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseListContextRepository')) {
                $this->studyCourseListContextRepository = $this->objectManager->get(
                    'In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseListContextRepository'
                );
            } else {
                $this->studyCourseListContextRepository = $this->objectManager->get(
                    'In2code\\In2studyfinderExtend\\Domain\\Repository\\StudyCourseRepository'
                );
            }
        }

        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        // cache $this->filters
        if (ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = $this->getCacheIdentifierForStudyCourses($this->settings['filters']);

            if ($this->cacheInstance->has($cacheIdentifier)) {
                $this->filters = $this->cacheInstance->get($cacheIdentifier);
            } else {
                $this->setFilters();
                $this->cacheInstance->set($cacheIdentifier, $this->filters, ['in2studyfinder']);
            }
        } else {
            $this->setFilters();
        }
    }

    /**
     * Inject Study Course List Context Repository
     *
     * @param StudyCourseListContextRepository $studyCourseListContextRepository
     */
    public function injectStudyCourseListContextRepository(
        StudyCourseListContextRepository $studyCourseListContextRepository
    ) {
        $this->studyCourseListContextRepository = $studyCourseListContextRepository;
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
                $cacheIdentifier = $this->getCacheIdentifierForStudyCourses($searchOptions);

                $foundStudyCourses = $this->cacheInstance->get($cacheIdentifier);

                if (!$foundStudyCourses) {
                    $foundStudyCourses = $this->processSearch($searchOptions);
                    $this->cacheInstance->set($cacheIdentifier, $foundStudyCourses, ['in2studyfinder']);
                }
            } else {
                $foundStudyCourses = $this->processSearch($searchOptions);
            }

            $studyCourses = $this->sortStudyCourses($foundStudyCourses);

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
                $this->settings['settingsPid'],
            ]
        );

        $defaultQuerySettings->setLanguageOverlayMode(true);
        $defaultQuerySettings->setLanguageMode('strict');

        return $defaultQuerySettings;
    }

    /**
     * set Filters
     */
    protected function setFilters()
    {
        foreach ((array)$this->settings['filters'] as $filterName => $filterProperties) {
            if ($filterProperties['type'] && $filterProperties['propertyPath'] && $filterProperties['frontendLabel']) {
                $frontendLabel = LocalizationUtility::translate(
                    $filterProperties['frontendLabel'],
                    'in2studyfinder'
                );

                if ($frontendLabel === null) {
                    $frontendLabel = $filterProperties['frontendLabel'];
                }

                $this->filters[$filterName] = [
                    'type' => $filterProperties['type'],
                    'propertyPath' => $filterProperties['propertyPath'],
                    'frontendLabel' => $frontendLabel,
                ];

                switch ($filterProperties['type']) {
                    case 'object':
                        $fullQualifiedRepositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName(
                            $filterProperties['objectModel']
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
            } else {
                $this->logger->log(
                    LogLevel::WARNING,
                    'Not a valid Typoscript Filter configuration! Ignore Filter: ' . $filterName,
                    [$filterName, $filterProperties]
                );
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
        $flexformOptions = $this->getSelectedFlexformOptions();

        if (ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = $this->getCacheIdentifierForStudyCourses($flexformOptions);

            $studyCourses = $this->cacheInstance->get($cacheIdentifier);

            // if no cache entry exists write cache
            if (!$studyCourses) {
                $studyCourses = $this->getStudyCoursesFromRepository($flexformOptions);
                $this->cacheInstance->set($cacheIdentifier, $studyCourses, ['in2studyfinder']);
            }
        } else {
            $studyCourses = $this->getStudyCoursesFromRepository($flexformOptions);
        }

        $studyCourses = $this->sortStudyCourses($studyCourses->toArray());

        return $studyCourses;
    }

    /**
     * @param array $flexformOptions
     * @return array|QueryResultInterface
     */
    protected function getStudyCoursesFromRepository($flexformOptions)
    {
        if (!empty($flexformOptions)) {
            $studyCourses = $this->processSearch($flexformOptions);
        } else {
            $studyCourses = $this->studyCourseListContextRepository->findAll();
        }

        return $studyCourses;
    }

    /**
     * @param array $options
     * @return string
     */
    protected function getCacheIdentifierForStudyCourses($options)
    {
        // create cache Identifier
        if (!empty($options)) {
            $cacheIdentifier = md5(
                FrontendUtility::getCurrentPageIdentifier(
                ) . '-' . $this->cObj->data['uid'] . '-' . FrontendUtility::getCurrentSysLanguageUid(
                ) . '-' . json_encode($options)
            );
        } else {
            $cacheIdentifier = md5(
                FrontendUtility::getCurrentPageIdentifier() . '-' . FrontendUtility::getCurrentSysLanguageUid(
                ) . '-' . 'allStudyCourses'
            );
        }

        return $cacheIdentifier;
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
     * @param StudyCourse $studyCourse
     *
     * @return void
     */
    protected function writePageMetadata($studyCourse)
    {
        if (!empty($studyCourse->getMetaPagetitle())) {
            FrontendUtility::getTyposcriptFrontendController()->page['title'] = $studyCourse->getMetaPagetitle();
        } else {
            FrontendUtility::getTyposcriptFrontendController()->page['title'] =
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
     * @return array
     */
    protected function processSearch($searchOptions)
    {
        $mergedOptions = [];

        // merge filter options to searchedOptions
        foreach ($searchOptions as $filterName => $searchedOptions) {
            $mergedOptions[$this->filters[$filterName]['propertyPath']] = $searchOptions[$filterName];
        }

        return $this->studyCourseListContextRepository->findAllFilteredByOptions($mergedOptions)->toArray();
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
                $property = $this->getPropertyByPropertyPath($course, $filter['propertyPath']);

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
                            $availableOptions[$filterName][1] = 'false';
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
                    /** @var AbstractDomainObject $property */
                    $property = $property->_getProperty($pathSegment);
                }
            }
        } else {
            $property = $obj->_getProperty($propertyPath);
        }

        return $property;
    }
}
