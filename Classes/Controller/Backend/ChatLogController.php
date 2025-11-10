<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller\Backend;

use In2code\In2studyfinder\Ai\Service\HistoryLogService;
use In2code\Lux\Backend\Buttons\NavigationGroupButton;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\GenericButton;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ChatLogController extends ActionController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly HistoryLogService $historyLogService,
        private readonly PageRepository $pageRepository,
        private readonly IconFactory $iconFactory,
    ) {
    }

    public function listAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setTitle('Chat Log Overview');

        // Get current page ID from request
        $pageId = (int)($this->request->getQueryParams()['id'] ?? 0);

        $moduleTemplate->assignMultiple([
            'groupedLogs' => $this->historyLogService->findOnPageSortedByPlugins($pageId),
            'page' => $this->pageRepository->getPage($pageId),
        ]);

        return $moduleTemplate->renderResponse('Backend/ChatLog/List.html');
    }

    public function showAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setTitle('Chat Log Details');

        $logId = (int)($this->request->getArgument('logId') ?? 0);
        $chatLog = $this->historyLogService->findByUid($logId);

        $moduleTemplate->assignMultiple([
            'chatLog' => $chatLog,
            'logId' => $logId
        ]);

        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $deleteButton = GeneralUtility::makeInstance(GenericButton::class)
            ->setTag('a')
            ->setTitle(LocalizationUtility::translate('LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_mod_chatlog.xlf:list.action.delete' ))
            ->setLabel(LocalizationUtility::translate('LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_mod_chatlog.xlf:list.action.delete' ))
            ->setHref($this->uriBuilder->reset()->setArguments(['logId' => $logId])->uriFor('delete'))
            ->setClasses('btn btn-sm btn-danger t3js-modal-trigger')
            ->setIcon($this->iconFactory->getIcon('actions-delete'))
            ->setShowLabelText(true);
        $closeButton = GeneralUtility::makeInstance(GenericButton::class)
            ->setTag('a')
            ->setTitle(LocalizationUtility::translate('LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_mod_chatlog.xlf:show.back_to_list' ))
            ->setLabel(LocalizationUtility::translate('LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_mod_chatlog.xlf:show.back_to_list' ))
            ->setHref($this->uriBuilder->reset()->uriFor('list'))
            ->setClasses('btn btn-sm btn-secondary')
            ->setIcon($this->iconFactory->getIcon('actions-close'))
            ->setShowLabelText(true);

        $buttonBar->addButton($closeButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
        $buttonBar->addButton($deleteButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
        return $moduleTemplate->renderResponse('Backend/ChatLog/Show.html');
    }

    public function deleteAction(): ResponseInterface
    {
        $logId = (int)($this->request->getArgument('logId') ?? 0);

        if ($logId === 0) {
            $this->addFlashMessage(
                'Invalid log ID provided.',
                'Error',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('list');
        }

        try {
            $this->historyLogService->deleteByUid($logId);
            $this->addFlashMessage('Chat log entry has been successfully deleted.', 'Success');
        } catch (\Exception $e) {
            $this->addFlashMessage(
                'An error occurred while deleting the chat log entry: ' . $e->getMessage(),
                'Error',
                ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('list');
    }
}
