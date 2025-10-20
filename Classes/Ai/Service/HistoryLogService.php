<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use In2code\In2studyfinder\Domain\Repository\ChatLogRepository;
use In2code\In2studyfinder\Service\FeSessionService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class HistoryLogService
{
    public function __construct(
        protected FeSessionService $feSessionService,
        protected ChatLogRepository $chatLogRepository
    ) {
    }

    public function logHistory(array $messages, ServerRequestInterface $serverRequest, int $pluginUid): void
    {
        $sessionIdentifier = $this->feSessionService->getSessionIdentifier($serverRequest);
        $logEntry = $this->chatLogRepository->findBySessionAndPluginIdentifier($sessionIdentifier, $pluginUid);

        if ($logEntry === null) {
            $logEntry = [
                'session_id' => $sessionIdentifier,
                'messages' => json_encode($messages),
                'crdate' => time(),
                'plugin_id' => $pluginUid,
            ];

            $this->chatLogRepository->create($logEntry);
            return;
        }

        $logEntry['messages'] = json_encode($messages);
        $this->chatLogRepository->updateMessages($logEntry);
    }

    public function findOnPageSortedByPlugins(int $pageId): array
    {
        $groupedLogs = [];
        $chatLogs = $this->chatLogRepository->findOnPage($pageId);

        foreach ($chatLogs as $chatLog) {
            if (isset($chatLog['plugin_id']) === false) {
                continue;
            }

            if (isset($groupedLogs[$chatLog['plugin_id']]) === false) {
                $plugin = BackendUtility::getRecord('tt_content', $chatLog['plugin_id']);
                $groupedLogs[$chatLog['plugin_id']] = $plugin;
            }

            $groupedLogs[$chatLog['plugin_id']]['logs'][] = $this->decodeMessages($chatLog);
        }

        return $groupedLogs;
    }

    public function findByUid(int $logId): ?array
    {
        $chatLog = $this->chatLogRepository->findByUid($logId);
        return $chatLog === null ? null : $this->decodeMessages($chatLog);
    }

    protected function decodeMessages(array $logEntry): array
    {
        $messages = json_decode($logEntry['messages'] ?? '[]', true);
        $logEntry['messages'] = $messages;
        return $logEntry;
    }
}
