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

use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Domain\Model\TtContent;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use In2code\In2studyfinder\Utility\VersionUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Property\Exception;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StudyCourseController extends AbstractController
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
     * @var Response
     */
    protected $response = null;

    /**
     * Use this instead of __construct, because extbase will inject dependencies *after* construnction of an object
     *
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function initializeAction()
    {
        parent::initializeAction();

        if (ConfigurationUtility::isCachingEnabled()) {
            $this->cacheInstance =
                GeneralUtility::makeInstance(CacheManager::class)->getCache('in2studyfinder');
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
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function listAction()
    {
        $this->forward('filter');
    }

    /**
     * Strip empty options from incoming (selected) filters
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeFilterAction()
    {
        if ($this->request->hasArgument('searchOptions')) {
            $searchOptions = array_filter((array)$this->request->getArgument('searchOptions'));
            $this->request->setArgument('searchOptions', $searchOptions);
            if (ConfigurationUtility::isPersistentFilterEnabled()) {
                $this
                    ->getTypoScriptFrontendController()
                    ->fe_user
                    ->setAndSaveSessionData('tx_in2studycourse_filter', $searchOptions);
            }
        }

        /*
         * add the flexform settings to the settings if the request is an ajax request
         */
        if (GeneralUtility::_GP('type') === '1308171055' && GeneralUtility::_GP('ce')) {
            $this->settings =
                array_merge_recursive(
                    $this->settings,
                    ExtensionUtility::getFlexFormSettingsByUid(GeneralUtility::_GP('ce'))
                );
        } else {
            $this->logger->log(
                LogLevel::ERROR,
                'Incorrect parameters of the Ajax request. Flexform settings could not be set! Maybe the extension\'s layout has been overwritten?',
                []
            );
        }
    }

    /**
     * @param array $searchOptions
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function filterAction(array $searchOptions = [])
    {
        if (empty($searchOptions) && ConfigurationUtility::isPersistentFilterEnabled()) {
            // No search options have been provided and no filter have been predefined in the Plugin's FlexForm
            // so we assume the user came back to the list page through another direct link.
            // Do not run this in the initializeFilterAction because it must also be applied for listAction.
            $searchOptions = $this
                ->getTypoScriptFrontendController()
                ->fe_user
                ->getSessionData('tx_in2studycourse_filter');
            if (empty($searchOptions)) {
                $searchOptions = [];
            }
        }

        $searchOptions =
            ArrayUtility::arrayMergeRecursiveOverrule(
                $searchOptions,
                $this->getSelectedFlexformOptions(),
                false,
                false
            );

        $studyCourses = $this->processSearch($searchOptions);

        /*
         * assign the current content element record to the view
         */
        if (GeneralUtility::_GP('type') === null) {
            $contentObj = $this->configurationManager->getContentObject();
            $this->view->assign('data', $contentObj->data);
        }

        $this->view->assignMultiple(
            [
                'searchedOptions' => $searchOptions,
                'filters' => $this->getFrontendFilters(),
                'availableFilterOptions' => $this->getAvailableFilterOptionsFromQueryResult($studyCourses),
                'studyCourseCount' => count($studyCourses),
                'studyCourses' => $studyCourses,
                'currentTypo3MajorVersion' => VersionUtility::getCurrentTypo3MajorVersion(),
                'settings' => $this->settings
            ]
        );
    }

    /**
     * WORKAROUND
     *
     * @see BackendController->listAction
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function getCoursesJsonAction()
    {

        if ($this->request->hasArgument('courseList')) {
            $courses = $this->studyCourseRepository->getCoursesWithUidIn(
                (array)$this->request->getArgument('courseList')
            )->toArray();
            $return = serialize($courses);

        } else {
            $return = 'the Required Arguments "courseList" is not set';
        }

        return json_encode($return);
    }

    /**
     * @param StudyCourseInterface|null $studyCourse
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function detailAction(StudyCourseInterface $studyCourse = null)
    {
        if ($studyCourse) {
            $this->writePageMetadata($studyCourse);
            $this->view->assign('studyCourse', $studyCourse);
        } else {
            $this->redirect('listAction', null, null, null, $this->settings['flexform']['studyCourseListPage']);
        }
    }

    /**
     * @param array $searchOptions
     * @return array|mixed
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function processSearch(array $searchOptions)
    {
        $mergedOptions = [];

        foreach ($searchOptions as $filterName => $searchedOptions) {
            $mergedOptions[$this->filters[$filterName]['propertyPath']] = $searchedOptions;
        }

        if ($this->isAjaxRequest()) {
            $storagePids = $this->getContentElementStoragePids((int)GeneralUtility::_GET('ce'));
            if (!empty($storagePids)) {
                $mergedOptions['storagePids'] = $storagePids;
            }
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
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
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
     * return the filters for the frontend
     *
     * @return array
     */
    protected function getFrontendFilters()
    {
        $filters = [];
        $selectedFlexformOptions = $this->getSelectedFlexformOptions();

        foreach ($this->filters as $filterName => $filter) {
            // disable filters in the frontend if the same filter is set in the backend plugin
            if (array_key_exists($filterName, $selectedFlexformOptions)
                && $selectedFlexformOptions[$filterName] !== '') {
                $filter['disabledInFrontend'] = 1;
            }
            if ($filter['disabledInFrontend'] === 0) {
                $filters[$filterName] = $filter;
            }
        }

        return $filters;
    }

    /**
     * Applies all filters set in the Plugin's FlexForm configuration and puts the result in $this->filters
     */
    protected function setFilters()
    {
        foreach ((array)$this->settings['filters'] as $filterName => $filterProperties) {
            if ($filterProperties['type'] && $filterProperties['propertyPath'] && $filterProperties['frontendLabel']) {
                $frontendLabel = LocalizationUtility::translate($filterProperties['frontendLabel'], 'in2studyfinder');
                if ($frontendLabel === null) {
                    $frontendLabel = $filterProperties['frontendLabel'];
                }

                $disabledInFrontend = 0;

                if ($filterProperties['disabledInFrontend'] === '1') {
                    $disabledInFrontend = 1;
                }

                $this->filters[$filterName] = [
                    'type' => $filterProperties['type'],
                    'propertyPath' => $filterProperties['propertyPath'],
                    'frontendLabel' => $frontendLabel,
                    'disabledInFrontend' => $disabledInFrontend,
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
     * @param StudyCourseInterface $studyCourse
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
     * @param array $searchOptions
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function searchAndSortStudyCourses(array $searchOptions)
    {
        $studyCourses = $this
            ->getStudyCourseRepository()
            ->findAllFilteredByOptions($searchOptions)
            ->toArray();

        if (array_key_exists(0, $studyCourses)) {
            usort($studyCourses, [$studyCourses[0], 'cmpObj']);
        }

        return $studyCourses;
    }

    /**
     * @param array $studyCourses
     * @return array
     */
    protected function getAvailableFilterOptionsFromQueryResult($studyCourses)
    {
        $availableOptions = [];

        foreach ($this->filters as $filterName => $filter) {
            /** @var $studyCourse StudyCourseInterface */
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

    /**
     * @param integer $contentElementUid
     * @return array
     */
    protected function getContentElementStoragePids($contentElementUid)
    {
        $storagePids = [];
        $pluginRecord = BackendUtility::getRecord(TtContent::TABLE, $contentElementUid, '*');

        if ($pluginRecord['pages'] !== '') {
            $storagePids = GeneralUtility::intExplode(',', $pluginRecord['pages']);
        }

        return $storagePids;
    }

    /**
     * @return bool
     */
    protected function isAjaxRequest()
    {
        $isAjaxRequest = false;
        if ((int)GeneralUtility::_GET('studyFinderAjaxRequest') === 1) {
            $isAjaxRequest = true;
        }

        return $isAjaxRequest;
    }
}
