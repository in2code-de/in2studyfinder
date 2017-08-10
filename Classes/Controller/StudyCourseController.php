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
use In2code\In2studyfinder\Utility\FrontendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\Exception;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StudyCourseController extends ActionController
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var FrontendInterface
     */
    protected $cacheInstance = null;

    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * @var Response
     */
    protected $response = null;

    /**
     * initialize action
     */
    protected function initializeAction()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(static::class);

        if (ConfigurationUtility::isCachingEnabled()) {
            $this->cacheInstance = GeneralUtility::makeInstance(CacheManager::class)->getCache('in2studyfinder');
        }

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
     * The list action is nothing else than the filter action but
     * without any search options (or they are predefined in the FlexForm options)
     */
    public function listAction()
    {
        $this->filterAction();
    }

    /**
     * @return void
     */
    public function initializeFilterAction()
    {
        if ($this->request->hasArgument('searchOptions')) {
            $searchOptions = (array)$this->request->getArgument('searchOptions');
            $this->request->setArgument('searchOptions', array_filter($searchOptions));
        }
    }

    /**
     * @param array $searchOptions
     * @return void
     */
    public function filterAction($searchOptions = [])
    {
        if (!empty($searchOptions)) {
            $this->view->assign('searchedOptions', $searchOptions);
        } else {
            $searchOptions = $this->getSelectedFlexformOptions();
        }

        $studyCourses = $this->processSearch($searchOptions);

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
            $this->getTypoScriptFrontendController()->pageNotFoundAndExit();
        }
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
                            $defaultQuerySettings = $this->objectManager->get(QuerySettingsInterface::class);
                            $defaultQuerySettings->setStoragePageIds([$this->settings['settingsPid']]);
                            $defaultQuerySettings->setLanguageOverlayMode(true);
                            $defaultQuerySettings->setLanguageMode('strict');
                            
                            $repository = $this->objectManager->get($fullQualifiedRepositoryClassName);
                            $repository->setDefaultQuerySettings($defaultQuerySettings);

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
     * @param array $options
     * @return string
     */
    protected function getCacheIdentifierForStudyCourses($options)
    {
        // create cache Identifier
        if (empty($options)) {
            $optionsIdentifier = 'allStudyCourses';
        } else {
            $optionsIdentifier = json_encode($options);
        }

        return md5(
            FrontendUtility::getCurrentPageIdentifier()
            . '-'
            . FrontendUtility::getCurrentSysLanguageUid()
            . '-'
            . $optionsIdentifier
        );
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

        foreach ($searchOptions as $filterName => $searchedOptions) {
            $mergedOptions[$this->filters[$filterName]['propertyPath']] = $searchedOptions;
        }

        if (ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = $this->getCacheIdentifierForStudyCourses($mergedOptions);
            $studyCourses = $this->cacheInstance->get($cacheIdentifier);

            if (!$studyCourses) {
                $studyCourses = $this->searchAndSortStudyCourses($mergedOptions);
                $this->cacheInstance->set($cacheIdentifier, $studyCourses, ['in2studyfinder']);
            }
        } else {
            $studyCourses = $this->searchAndSortStudyCourses($mergedOptions);
        }

        return $studyCourses;
    }

    /**
     * @param array $searchOptions
     * @return array
     */
    protected function searchAndSortStudyCourses(array $searchOptions)
    {
        $studyCourses = $this
            ->objectManager
            ->get(StudyCourseRepository::class)
            ->findAllFilteredByOptions($searchOptions)
            ->toArray();
        usort($studyCourses, array(StudyCourse::class, 'cmpObj'));
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
            /** @var $studyCourse StudyCourse */
            foreach ($studyCourses as $studyCourse) {
                $property = ObjectAccess::getPropertyPath($studyCourse, $filter['propertyPath']);

                switch ($filter['type']) {
                    case 'object':
                        if ($property instanceof ObjectStorage) {
                            foreach ($property as $obj) {
                                $availableOptions[$filterName][$obj->getUid()] = $obj->getUid();
                            }
                        } elseif ($property instanceof AbstractDomainObject) {
                            $availableOptions[$filterName][$property->getUid()] = $property->getUid();
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
                        break;
                }
            }
        }

        return $availableOptions;
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
