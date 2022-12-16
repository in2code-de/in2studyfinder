<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\Domain\Service\CourseService;
use In2code\In2studyfinder\Service\ExportService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class BackendController extends AbstractController
{
    protected CourseService $courseService;

    /**
     * @var StudyCourseRepository
     */
    protected $studyCourseRepository;

    public function __construct(StudyCourseRepository $studyCourseRepository, CourseService $courseService)
    {
        $this->studyCourseRepository = $studyCourseRepository;
        $this->courseService = $courseService;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
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

        if (empty($studyCourses)) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noCourses.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noCourses.title', 'in2studyfinder'),
                AbstractMessage::WARNING
            );
        } else {
            $propertyArray =
                $this->courseService->getCourseProperties(
                    $studyCourses[0],
                    $this->settings['backend']['export']['excludedPropertiesForExport'] ?? []
                );
        }

        $itemsPerPage = $this->settings['pagination']['itemsPerPage'] ?? 10;

        if ($this->request->hasArgument('itemsPerPage')) {
            $itemsPerPage = $this->request->getArgument('itemsPerPage');
        }

        $this->view->assignMultiple(
            [
                'studyCourses' => $studyCourses,
                'exportDataProvider' => $possibleExportDataProvider,
                'availableFieldsForExport' => $propertyArray,
                'sysLanguages' => $this->getSysLanguages(),
                'itemsPerPage' => $itemsPerPage
            ]
        );

        return $this->htmlResponse();
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function exportAction(
        string $exporter,
        int $recordLanguage,
        array $selectedProperties,
        array $courseList
    ): void {
        if (empty($selectedProperties) || empty($courseList)) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.notAllRequiredFieldsSet.title', 'in2studyfinder'),
                AbstractMessage::ERROR
            );

            $this->forward('list');
        }

        $courses = $this->studyCourseRepository->findByUidsAndLanguage($courseList, (int)$recordLanguage);

        $exportService =
            GeneralUtility::makeInstance(ExportService::class, $exporter, $selectedProperties, $courses->toArray());

        $exportService->export();
    }

    protected function getSysLanguages(): array
    {
        $sysLanguages = [
            0 => 'default'
        ];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $languageRecords = $queryBuilder
            ->select('*')
            ->from('sys_language')
            ->where($queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)))
            ->orderBy('sorting')
            ->execute()->fetchAll();

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
                        AbstractMessage::ERROR
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
                AbstractMessage::ERROR
            );
        }

        if (!isset($this->settings['settingsPid']) || empty($this->settings['settingsPid'])) {
            $this->addFlashMessage(
                LocalizationUtility::translate('messages.noSettingsPid.body', 'in2studyfinder'),
                LocalizationUtility::translate('messages.noSettingsPid.title', 'in2studyfinder'),
                AbstractMessage::ERROR
            );
        }
    }
}
