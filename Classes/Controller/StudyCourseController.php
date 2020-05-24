<?php

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Domain\Model\TtContent;
use In2code\In2studyfinder\Domain\Repository\FacultyRepository;
use In2code\In2studyfinder\Service\FilterService;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use In2code\In2studyfinder\Utility\RecordUtility;
use In2code\In2studyfinder\Utility\VersionUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Property\Exception;

/**
 * StudyCourseController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StudyCourseController extends AbstractController
{
    /**
     * @var FilterService
     */
    protected $filterService;

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
                $this->logger->error(
                    'Incorrect parameters of the Ajax request. Flexform settings could not be set! Maybe the extension\'s layout has been overwritten?',
                    ['additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]]
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
            array_replace(
                $this->filterService->getPluginFilterRestrictions(),
                $searchOptions
            );

        $studyCourses = $this->processSearch($searchOptions);

        $this->view->assignMultiple(
            [
                'searchedOptions' => $searchOptions,
                'filters' => $this->filterService->getEnabledFrontendFilter(),
                'availableFilterOptions' => $this->filterService->getAvailableFilterOptions($studyCourses),
                'studyCourseCount' => count($studyCourses),
                'studyCourses' => $studyCourses,
                'currentTypo3MajorVersion' => VersionUtility::getCurrentTypo3MajorVersion(),
                'settings' => $this->settings,
                'data' => $this->getPluginRecord(),
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
            $this->logger->error(
                'the Requested Argument "courseList" is not set.',
                ['additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]]
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
        $filter = $this->filterService->getFilter();

        foreach ($searchOptions as $filterName => $searchedOptions) {
            $searchOptions[$filter[$filterName]['propertyPath']] = $searchedOptions;
            if ($filter[$filterName]['propertyPath'] !== $filterName) {
                unset($searchOptions[$filterName]);
            }
        }

        if ($this->isAjaxRequest()) {
            $storagePids = $this->getContentElementStoragePids((int)GeneralUtility::_GET('ce'));
            if (!empty($storagePids)) {
                $searchOptions['storagePids'] = $storagePids;
            }
        }

        if (ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = $this->getCacheIdentifierForStudyCourses($searchOptions);

            $studyCourses = $this->cacheInstance->get($cacheIdentifier);

            if (!$studyCourses) {
                $studyCourses = $this->searchAndSortStudyCourses($searchOptions);
                $this->cacheInstance->set($cacheIdentifier, $studyCourses, ['in2studyfinder']);
            }
        } else {
            $studyCourses = $this->searchAndSortStudyCourses($searchOptions);
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
     * @return array
     */
    protected function getPluginRecord()
    {
        $pluginRecord = [];
        $type = GeneralUtility::_GP('type');
        $language = FrontendUtility::getCurrentSysLanguageUid();
        $pluginUid = (int)GeneralUtility::_GP('ce');

        $pluginRecords = RecordUtility::getRecordWithTranslations($pluginUid);

        if (empty($type)) {
            $contentObj = $this->configurationManager->getContentObject();
            $pluginRecord = $contentObj->data;
        } else {
            if (array_key_exists($language, $pluginRecords)) {
                $pluginRecord = $pluginRecords[$language];
            } else {
                $this->logger->error(
                    'No url parameter ce is set. Please check the ajax request in your network analyse tool if an filter is set',
                    ['additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]]
                );
            }
        }

        return $pluginRecord;
    }

    /**
     * @param FilterService $filterService
     */
    public function injectFilterService(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }
}
