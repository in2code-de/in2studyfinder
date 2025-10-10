<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use In2code\In2studyfinder\Domain\Repository\ChatLogRepository;
use In2code\In2studyfinder\Service\FeSessionService;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class HistoryLogService
{
    public function __construct(
        protected FeSessionService $feSessionService,
        protected ChatLogRepository $chatLogRepository
    ) {
    }

    public function logHistory(array $messages, ServerRequestInterface $serverRequest): void
    {
        $sessionIdentifier = $this->feSessionService->getSessionIdentifier($serverRequest);
        $logEntry = $this->chatLogRepository->findBySessionIdentifier($sessionIdentifier);

        if ($logEntry === null) {
            $logEntry = [
                'session_id' => $sessionIdentifier,
                'messages' => json_encode($messages),
                'crdate' => time(),
                'plugin_id' => $this->getPluginId($serverRequest),
            ];

            $this->chatLogRepository->create($logEntry);
            return;
        }

        $logEntry['messages'] = json_encode(array_merge(json_decode($logEntry['messages'], true), $messages));
        $this->chatLogRepository->update($logEntry);
    }

    protected function getPluginId(ServerRequestInterface $request): int
    {
        /** @var ContentObjectRenderer $contentObject */
        $contentObjectRenderer = $request->getAttribute('currentContentObject');
        $pluginId = $contentObjectRenderer?->data['uid'] ?? null;

        if ($pluginId === null) {
            throw new LogicException('Could not find plugin id', 1760097459);
        }

        return (int)$pluginId;
    }
}
