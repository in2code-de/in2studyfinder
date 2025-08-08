<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Listener;

use In2code\In2studyfinder\Settings\ExtensionSettingsInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PageContentPreviewRendering
{
    public function __construct(
        protected readonly FlexFormService $flexFormService,
        protected readonly SiteFinder $siteFinder,
        protected readonly ExtensionSettingsInterface $extensionSettings
    ) {
    }

    #[AsEventListener]
    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $record = $event->getRecord();

        if (!str_starts_with($record['CType'], 'in2studyfinder_')) {
            return;
        }

        $typoScriptSettings = $this->extensionSettings->getTypoScriptSettings();
        $flexForm = $this->flexFormService->convertFlexFormContentToArray($record['pi_flexform'] ?? '');
        $extendedVariables = [];

        if ($typoScriptSettings === []) {
            $extendedVariables['errors']['missingExtensionSettings'] = true;
        } else {
            $extendedVariables['recordStoragePages'] = $this->getRecordStoragePages($record);
            $extendedVariables['detailPage'] = BackendUtility::getRecord(
                'pages',
                (int)($flexForm['settings']['flexform']['studyCourseDetailPage'] ?? -1)
            );

            $extendedVariables['listPage'] = BackendUtility::getRecord(
                'pages',
                (int)($flexForm['settings']['flexform']['studyCourseListPage'] ?? -1)
            );
        }

        $record['extendedVariables'] = $extendedVariables;
        $event->setRecord($record);
    }

    protected function getRecordStoragePages(array $record): array
    {
        $storagePids = $this->extensionSettings->getConfiguredStoragePids();

        if ($storagePids === []) {
            return [];
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        return $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $storagePids
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

}
