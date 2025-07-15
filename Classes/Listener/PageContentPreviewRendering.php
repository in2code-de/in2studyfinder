<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Listener;

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Site\SiteFinder;

final class PageContentPreviewRendering
{
    public function __construct(protected readonly FlexFormService $flexFormService, protected readonly SiteFinder $siteFinder)
    {
    }

    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $record = $event->getRecord();

        if (!str_starts_with($record['CType'], 'in2studyfinder_')) {
            return;
        }

        $typoScriptSettings = ExtensionUtility::getExtensionSettings('in2studyfinder');
        $flexForm = $this->flexFormService->convertFlexFormContentToArray($record['pi_flexform'] ?? '');
        $extendedVariables = [];

        if ($typoScriptSettings === []) {
            $extendedVariables['errors']['missingExtensionSettings'] = true;
        } else {
            switch ($record['CType']) {
                case 'in2studyfinder_filter':
                    $extendedVariables['detailPage'] = BackendUtility::getRecord(
                        'pages',
                        (int)($flexForm['settings']['flexform']['studyCourseDetailPage'] ?? -1)
                    );

                    // filter configuration
                    break;
                case 'in2studyfinder_detail':
                    break;
                case 'in2studyfinder_fastSearch':
                    break;
            }
        }

        $record['extendedVariables'] = $extendedVariables;
        $event->setRecord($record);
    }
}
