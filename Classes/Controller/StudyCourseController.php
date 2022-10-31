<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Service\CourseService;
use In2code\In2studyfinder\Domain\Service\FacilityService;
use In2code\In2studyfinder\Property\TypeConverter\StudyCourseConverter;
use In2code\In2studyfinder\Service\FilterService;
use In2code\In2studyfinder\Utility\CacheUtility;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\FlexFormUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use In2code\In2studyfinder\Utility\RecordUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StudyCourseController extends AbstractController
{
    protected FilterService $filterService;

    protected CourseService $courseService;

    protected FacilityService $facilityService;

    public function __construct(
        FilterService $filterService,
        CourseService $courseService,
        FacilityService $facilityService
    ) {
        $this->filterService = $filterService;
        $this->courseService = $courseService;
        $this->facilityService = $facilityService;
    }

    /**
     * Strip empty options from incoming (selected) filters
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeFilterAction(): void
    {
        $this->filterService->initialize();

        if ($this->request->hasArgument('searchOptions')) {
            $searchOptions = array_filter((array)$this->request->getArgument('searchOptions'));
            $this->request->setArgument('searchOptions', $searchOptions);

            if (ConfigurationUtility::isPersistentFilterEnabled()) {
                FrontendUtility::getTyposcriptFrontendController()
                    ->fe_user
                    ->setAndSaveSessionData('tx_in2studycourse_filter', $searchOptions);
            }
        } else {
            if (ConfigurationUtility::isPersistentFilterEnabled()) {
                $this->request->setArgument(
                    'searchOptions',
                    FrontendUtility::getTyposcriptFrontendController()
                        ->fe_user
                        ->getSessionData('tx_in2studycourse_filter')
                );
            }
        }
    }

    /**
     * @param array $pluginInformation contains additional plugin information from ajax / fetch requests
     */
    public function filterAction(array $searchOptions = [], array $pluginInformation = []): void
    {
        if (!empty($pluginInformation)) {
            // if the current call is an ajax / fetch request
            $currentPluginRecord =
                RecordUtility::getRecordWithLanguageOverlay(
                    (int)$pluginInformation['pluginUid'],
                    (int)$pluginInformation['languageUid']
                );

            $this->settings =
                array_merge(
                    $this->settings,
                    FlexFormUtility::getFlexForm($currentPluginRecord['pi_flexform'], 'settings')
                );
        } else {
            $currentPluginRecord = $this->configurationManager->getContentObject()->data;
        }

        $studyCourses = $this->courseService->findBySearchOptions(
            $this->filterService->setSettings($this->settings)->prepareSearchOptions($searchOptions),
            $currentPluginRecord
        );

        $this->view->assignMultiple(
            [
                'searchedOptions' => $searchOptions,
                'filters' => $this->filterService->getFilter(),
                'availableFilterOptions' => $this->filterService->getAvailableFilterOptions($studyCourses),
                'studyCourseCount' => count($studyCourses),
                'studyCourses' => $studyCourses,
                'settings' => $this->settings,
                'data' => $currentPluginRecord
            ]
        );
    }

    /**
     * fastSearchAction
     */
    public function fastSearchAction(): void
    {
        $studyCourses =
            $this->courseService->findBySearchOptions([], $this->configurationManager->getContentObject()->data);

        $this->view->assignMultiple(
            [
                'studyCourseCount' => count($studyCourses),
                'facultyCount' => $this->facilityService->getFacultyCount($this->settings),
                'studyCourses' => $studyCourses,
                'settings' => $this->settings
            ]
        );
    }

    /**
     * converts the default course to the extended course if overwritten
     */
    public function initializeDetailAction(): void
    {
        if (
            array_key_exists(StudyCourse::class, $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']) &&
            !empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][StudyCourse::class]) &&
            $this->request->hasArgument('studyCourse')
        ) {
            $this->arguments->getArgument('studyCourse')
                ->getPropertyMappingConfiguration()
                ->setTypeConverter(
                    GeneralUtility::makeInstance(
                        StudyCourseConverter::class,
                        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][StudyCourse::class]['className']
                    )
                );
        }
    }

    /**
     * @param StudyCourse|null $studyCourse
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function detailAction(StudyCourse $studyCourse = null): void
    {
        if ($studyCourse) {
            $this->courseService->setPageTitleAndMetadata($studyCourse);
            CacheUtility::addCacheTags([$studyCourse]);

            $this->view->assign('studyCourse', $studyCourse);
        } else {
            $this->redirect('filterAction', null, null, null, $this->settings['flexform']['studyCourseListPage']);
        }
    }
}
