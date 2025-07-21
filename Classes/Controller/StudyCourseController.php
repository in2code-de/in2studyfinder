<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Service\CourseService;
use In2code\In2studyfinder\Domain\Service\FacilityService;
use In2code\In2studyfinder\Event\ModifyDetailActionFluidVariablesEvent;
use In2code\In2studyfinder\Event\ModifyFastSearchActionFluidVariablesEvent;
use In2code\In2studyfinder\Event\ModifyFilterActionFluidVariablesEvent;
use In2code\In2studyfinder\Property\TypeConverter\StudyCourseConverter;
use In2code\In2studyfinder\Service\FilterService;
use In2code\In2studyfinder\Utility\CacheUtility;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\FlexFormUtility;
use In2code\In2studyfinder\Utility\RecordUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\LanguageAspectFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StudyCourseController extends AbstractController
{
    public function __construct(
        protected FilterService $filterService,
        protected CourseService $courseService,
        protected FacilityService $facilityService
    ) {
    }

    /**
     * @param array $pluginInformation contains additional plugin information from ajax / fetch requests
     */
    public function filterAction(array $searchOptions = [], array $pluginInformation = []): ResponseInterface
    {
        $this->filterService->initialize();

        if ($pluginInformation !== []) {
            $site = $this->request->getAttribute('site');
            $siteLanguage = $site->getLanguageById((int)$pluginInformation['languageUid']);

            // if the current call is an ajax / fetch request
            $currentPluginRecord =
                RecordUtility::getRecordWithLanguageOverlay(
                    (int)$pluginInformation['pluginUid'],
                    LanguageAspectFactory::createFromSiteLanguage($siteLanguage)
                );

            $this->settings =
                array_merge(
                    $this->settings,
                    FlexFormUtility::getFlexForm($currentPluginRecord['pi_flexform'], 'settings')
                );
        } else {
            $currentPluginRecord = $this->request->getAttribute('currentContentObject')->data;
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

        $fluidVariables = [
            'searchedOptions' => $searchOptions,
            'filters' => $this->filterService->getFilter(),
            'availableFilterOptions' => $this->filterService->getAvailableFilterOptions($studyCourses),
            'studyCourseCount' => count($studyCourses),
            'studyCourses' => $studyCourses,
            'settings' => $this->settings,
            'data' => $currentPluginRecord
        ];

        $event = $this->eventDispatcher->dispatch(new ModifyFilterActionFluidVariablesEvent($this, $fluidVariables));
        $this->view->assignMultiple($event->getFluidVariables());

        return $this->htmlResponse();
    }

    /**
     * fastSearchAction
     */
    public function fastSearchAction(): ResponseInterface
    {
        $currentPluginRecord = $this->request->getAttribute('currentContentObject')->data;
        $studyCourses =
            $this->courseService->findBySearchOptions([], $currentPluginRecord);

        $fluidVariables =  [
            'studyCourseCount' => count($studyCourses),
            'facultyCount' => $this->facilityService->getFacultyCount($this->settings),
            'studyCourses' => $studyCourses,
            'settings' => $this->settings,
            'data' => $currentPluginRecord
        ];

        $event = $this->eventDispatcher->dispatch(new ModifyFastSearchActionFluidVariablesEvent($this, $fluidVariables));
        $this->view->assignMultiple($event->getFluidVariables());

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
     */
    public function detailAction(StudyCourse $studyCourse = null): ResponseInterface
    {
        if ($studyCourse !== null) {
            $this->courseService->setPageTitleAndMetadata($studyCourse);
            CacheUtility::addCacheTags([$studyCourse]);

            $fluidVariables = ['studyCourse' => $studyCourse];

            $event = $this->eventDispatcher->dispatch(new ModifyDetailActionFluidVariablesEvent($this, $fluidVariables));
            $this->view->assignMultiple($event->getFluidVariables());
        } else {
            $studyCourseListPage = $this->settings['flexform']['studyCourseListPage'] ?? '';
            return $this->redirect('filterAction', null, null, null, $studyCourseListPage);
        }

        return $this->htmlResponse();
    }
}
