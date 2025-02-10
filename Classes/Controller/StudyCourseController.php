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
use Psr\Http\Message\ResponseInterface;
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
     * @param array $pluginInformation contains additional plugin information from ajax / fetch requests
     */
    public function filterAction(array $searchOptions = [], array $pluginInformation = []): ResponseInterface
    {
        $this->filterService->initialize();

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

        $this->filterService->setSettings($this->settings);
        $searchOptions = $this->filterService->sanitizeSearch($searchOptions);

        if (ConfigurationUtility::isPersistentFilterEnabled()) {
            $searchOptions = $this->filterService->loadOrSetPersistedFilter($searchOptions);
        }

        $studyCourses = $this->courseService->findBySearchOptions(
            $this->filterService->resolveFilterPropertyPath($searchOptions),
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

        return $this->htmlResponse();
    }

    /**
     * fastSearchAction
     */
    public function fastSearchAction(): ResponseInterface
    {
        $currentPluginRecord = $this->configurationManager->getContentObject()->data;
        $studyCourses =
            $this->courseService->findBySearchOptions([], $currentPluginRecord);

        $this->view->assignMultiple(
            [
                'studyCourseCount' => count($studyCourses),
                'facultyCount' => $this->facilityService->getFacultyCount($this->settings),
                'studyCourses' => $studyCourses,
                'settings' => $this->settings,
                'data' => $currentPluginRecord
            ]
        );

        return $this->htmlResponse();
    }

    /**
     * converts the default course to the extended course if overwritten
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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
    public function detailAction(StudyCourse $studyCourse = null): ResponseInterface
    {
        if ($studyCourse) {
            $this->courseService->setPageTitleAndMetadata($studyCourse);
            CacheUtility::addCacheTags([$studyCourse]);

            $this->view->assign('studyCourse', $studyCourse);
        } else {
           return $this->redirect('filterAction', null, null, null, $this->settings['flexform']['studyCourseListPage']);
        }

        return $this->htmlResponse();
    }
}
