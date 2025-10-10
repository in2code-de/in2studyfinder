<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller\Backend;

use In2code\In2studyfinder\Ai\Service\HistoryLogService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ChatLogController extends ActionController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly HistoryLogService $historyLogService,
        private readonly PageRepository $pageRepository,
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

        return $moduleTemplate->renderResponse('Backend/ChatLog/Show.html');
    }
}
