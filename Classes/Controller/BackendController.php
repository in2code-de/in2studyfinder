<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\Domain\Service\CourseService;
use In2code\In2studyfinder\Service\ExportService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BackendController extends AbstractController
{
    public function __construct(
        protected StudyCourseRepository $studyCourseRepository,
        protected CourseService $courseService,
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected PageRenderer $pageRenderer,
        private readonly ConnectionPool $connectionPool
    ) {
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function listAction(): ResponseInterface
    {
        $this->validateSettings();

        $recordLanguage = 0;
        $propertyArray = [];

        if ($this->request->hasArgument('recordLanguage')) {
            $recordLanguage = (int)$this->request->getArgument('recordLanguage');
        }

        $studyCourses =
            $this->studyCourseRepository->findAllForExport(
                $recordLanguage,
                (bool)($this->settings['backend']['export']['includeDeleted'] ?? false),
                (bool)($this->settings['backend']['export']['includeHidden'] ?? false)
            );

        $possibleExportDataProvider = $this->getPossibleExportDataProvider();

        if ($studyCourses->count() === 0) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noCourses.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noCourses.title', 'in2studyfinder'),
                ContextualFeedbackSeverity::WARNING
            );
        } else {
            $propertyArray =
                $this->courseService->getCourseProperties(
                    $studyCourses->getFirst(),
                    $this->settings['backend']['export']['excludedPropertiesForExport'] ?? []
                );
        }

        $currentPage = $this->request->hasArgument('currentPageNumber')
            ? (int)$this->request->getArgument('currentPageNumber')
            : 1;

        $itemsPerPage = (int)($this->settings['pagination']['itemsPerPage'] ?? 10);
        if ($this->request->hasArgument('itemsPerPage')) {
            $itemsPerPage = (int)$this->request->getArgument('itemsPerPage');
        }
        $maximumLinks = 15;

        $paginator = new QueryResultPaginator(
            $studyCourses,
            $currentPage,
            $itemsPerPage,
        );
        $pagination = new SlidingWindowPagination(
            $paginator,
            $maximumLinks,
        );

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple(
            [
                'studyCourses' => $studyCourses,
                'exportDataProvider' => $possibleExportDataProvider,
                'availableFieldsForExport' => $propertyArray,
                'sysLanguages' => $this->getSysLanguages(),
                'itemsPerPage' => $itemsPerPage,
                'pagination' => $pagination,
                'paginator' => $paginator,
                'currentPageNumber' => $currentPage
            ]
        );
        $this->pageRenderer->loadJavaScriptModule('@in2code/in2studyfinder/backend');
        $this->pageRenderer->addCssFile('EXT:in2studyfinder/Resources/Public/Css/backend.css');

        return $moduleTemplate->renderResponse('Backend/List');
    }

    /**
     * @throws InvalidQueryException
     */
    public function exportAction(
        string $exporter,
        int $recordLanguage,
        array $selectedProperties,
        array $courseList
    ): ResponseInterface {
        if ($selectedProperties === [] || $courseList === []) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.title', 'in2studyfinder'),
                ContextualFeedbackSeverity::ERROR
            );
            return new ForwardResponse('list');
        }

        $courses = $this->studyCourseRepository->findByUidsAndLanguage($courseList, $recordLanguage);

        $exportService =
            GeneralUtility::makeInstance(ExportService::class, $exporter, $selectedProperties, $courses->toArray());

        $exportService->export();
        return $this->htmlResponse();
    }

    protected function getSysLanguages(): array
    {
        $sysLanguages = [
            0 => 'default'
        ];

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_language');
        $languageRecords = $queryBuilder
            ->select('*')
            ->from('sys_language')
            ->where($queryBuilder->expr()->eq('hidden',
                $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)))
            ->orderBy('sorting')
            ->executeQuery()->fetchAllAssociative();

        foreach ($languageRecords as $languageRecord) {
            $sysLanguages[(int)$languageRecord['uid']] =
                LocalizationUtility::translate(
                    'LLL:EXT:core/Resources/Private/Language/db.xlf:sys_language.language_isocode.' .
                    $languageRecord['language_isocode']
                );
        }

        return $sysLanguages;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getPossibleExportDataProvider(): array
    {
        $possibleDataProvider = [];

        if (
            isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes'])
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes'] as $providerName => $providerClass) {
                if (!class_exists($providerClass)) {
                    $this->addFlashMessage(
                        'export provider class "' . $providerClass . '" was not found',
                        'export provider class not found',
                        ContextualFeedbackSeverity::ERROR
                    );
                } else {
                    $possibleDataProvider[$providerName] = $providerClass;
                }
            }
        }

        return $possibleDataProvider;
    }

    protected function validateSettings(): void
    {
        if (!isset($this->settings['storagePid']) || empty($this->settings['storagePid'])) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noStoragePid.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noStoragePid.title', 'in2studyfinder'),
                ContextualFeedbackSeverity::ERROR
            );
        }

        if (!isset($this->settings['settingsPid']) || empty($this->settings['settingsPid'])) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noSettingsPid.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noSettingsPid.title', 'in2studyfinder'),
                ContextualFeedbackSeverity::ERROR
            );
        }
    }
}
