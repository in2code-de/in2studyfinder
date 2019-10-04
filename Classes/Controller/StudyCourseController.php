<?php

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Domain\Model\TtContent;
use In2code\In2studyfinder\Domain\Repository\FacultyRepository;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use In2code\In2studyfinder\Utility\VersionUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\QueryGenerator;
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
     * @var array
     */
    protected $pluginRecord;

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

        $this->setPluginRecord();

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
                FrontendUtility::getTyposcriptFrontendController()
                    ->fe_user
                    ->setAndSaveSessionData('tx_in2studycourse_filter', $searchOptions);
            }
        }

        /*
         * add the flexform settings to the settings if the request is an ajax request
         */
        if ($this->isAjaxRequest()) {
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
            $searchOptions =
                FrontendUtility::getTyposcriptFrontendController()
                    ->fe_user
                    ->getSessionData('tx_in2studycourse_filter');
            if (empty($searchOptions)) {
                $searchOptions = [];
            }
        }

        $searchOptions =
            array_replace_recursive(
                $searchOptions,
                $this->getSelectedFlexformOptions()
            );

        $studyCourses = $this->processSearch($searchOptions);

        $this->view->assignMultiple(
            [
                'searchedOptions' => $searchOptions,
                'filters' => $this->getFrontendFilters(),
                'availableFilterOptions' => $this->getAvailableFilterOptionsFromQueryResult($studyCourses),
                'studyCourseCount' => count($studyCourses),
                'studyCourses' => $studyCourses,
                'currentTypo3MajorVersion' => VersionUtility::getCurrentTypo3MajorVersion(),
                'settings' => $this->settings,
                'data' => $this->pluginRecord,
                'isAjaxRequest' => $this->isAjaxRequest()
            ]
        );
    }

    /**
     * fastSearchAction
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function fastSearchAction()
    {
        $studyCourses = $this->processSearch([]);

        $this->view->assignMultiple(
            [
                'studyCourseCount' => count($studyCourses),
                'facultyCount' => $this->getFacultyCount(),
                'studyCourses' => $studyCourses,
                'data' => $this->configurationManager->getContentObject()->data,
                'settings' => $this->settings
            ]
        );
    }

    /**
     * @return int
     */
    protected function getFacultyCount()
    {
        if (ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = md5('facultyCount');
            $facultyCount = $this->cacheInstance->get($cacheIdentifier);

            if (!$facultyCount) {
                $facultyCount = $this->countFaculties();

                $this->cacheInstance->set($cacheIdentifier, $facultyCount, ['in2studyfinder']);
            }
        } else {
            $facultyCount = $this->countFaculties();
        }

        if (!empty($facultyCount)) {
            return $facultyCount;
        }

        return 0;
    }

    /**
     * @return integer
     */
    private function countFaculties()
    {
        $facultyRepository = $this->objectManager->get(FacultyRepository::class);
        $defaultQuerySettings = $this->objectManager->get(QuerySettingsInterface::class);
        $defaultQuerySettings->setStoragePageIds([$this->settings['settingsPid']]);
        $facultyRepository->setDefaultQuerySettings($defaultQuerySettings);

        return $facultyRepository->countAll();
    }

    /**
     * WORKAROUND
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @see BackendController->listAction
     *
     */
    public function getCoursesJsonAction()
    {
        $return = '';

        if ($this->request->hasArgument('courseList')) {
            $courses = $this->studyCourseRepository->getCoursesWithUidIn(
                (array)$this->request->getArgument('courseList')
            )->toArray();
            $return = serialize($courses);

        } else {
            $this->logger->log(
                LogLevel::ERROR,
                'the Requested Argument "courseList" is not set.',
                [__METHOD__ . ' on Line ' . __LINE__]
            );
        }

        return json_encode($return);
    }

    /**
     * @param StudyCourse|null $studyCourse
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
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
            FrontendUtility::getTyposcriptFrontendController()->pageNotFoundAndExit();
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
                            $this->filters[$filterName]['filterOptions'] = $repository->findAll()->toArray();
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
     * @param integer $contentElementUid
     * @return array
     */
    protected function getContentElementStoragePids($contentElementUid)
    {
        $storagePids = [];
        $pluginRecord = BackendUtility::getRecord(TtContent::TABLE, $contentElementUid, '*');

        if ($pluginRecord['pages'] !== '') {
            $storagePids = GeneralUtility::intExplode(',', $pluginRecord['pages']);

            // add recursive pids if recursive is set in the plugin
            if ($pluginRecord['recursive'] > 0) {
                $queryGenerator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(QueryGenerator::class);
                $recursiveStoragePids = '';
                foreach ($storagePids as $storagePid) {
                    $recursiveStoragePids .= $queryGenerator->getTreeList(
                            $storagePid,
                            $pluginRecord['recursive'],
                            0,
                            1
                        ) . ',';
                }

                $storagePids = GeneralUtility::trimExplode(',', $recursiveStoragePids, 1);
            }
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

    /**
     *
     */
    protected function setPluginRecord()
    {
        $pluginRecord = [];

        if (empty(GeneralUtility::_GP('type'))) {
            $contentObj = $this->configurationManager->getContentObject();
            $pluginRecord = $contentObj->data;
        } else {
            if (!empty(GeneralUtility::_GP('ce'))) {
                $queryBuilder =
                    $this->objectManager->get(ConnectionPool::class)->getQueryBuilderForTable(TtContent::TABLE);
                $record = $queryBuilder
                    ->select('*')
                    ->from(TtContent::TABLE)
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter((int)GeneralUtility::_GP('ce'), \PDO::PARAM_INT)
                        )
                    )
                    ->execute()->fetch();

                $pluginRecord = $record;
            } else {
                $this->logger->log(
                    LogLevel::ERROR,
                    'No url parameter ce is set. Please check the ajax request in your network analyse tool if an filter is set',
                    []
                );
            }
        }

        $this->pluginRecord = $pluginRecord;
    }
}
