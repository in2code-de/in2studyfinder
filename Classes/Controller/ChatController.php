<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use GuzzleHttp\Exception\ClientException;
use In2code\In2studyfinder\Ai\Service\ChatService;
use In2code\In2studyfinder\Ai\Service\HistoryLogService;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ChatController extends ActionController
{
    public function __construct(
        private readonly ChatService $chatService,
        private readonly HistoryLogService $historyLogService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function indexAction(): ResponseInterface
    {
        $this->chatService->deleteHistory($this->request);
        return $this->htmlResponse();
    }

    public function chatAction(): ResponseInterface
    {
        try {
            $messages = $this->chatService->chat($this->request, $this->settings);
            $this->historyLogService->logHistory($messages, $this->request);
            $message = end($messages)['content'] ?? '';
            return $this->jsonResponse(json_encode(['success' => true, 'message' => $message]));
        } catch (ClientException $exception) {
            $this->logger->error($exception->getResponse()->getBody()->getContents());
            return $this->jsonResponse(json_encode(['success' => false, 'errorCode' => $exception->getCode()]));
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return $this->jsonResponse(json_encode(['success' => false, 'errorCode' => $throwable->getCode()]));
        }
    }

    public function deleteHistoryAction(): ResponseInterface
    {
        $this->chatService->deleteHistory($this->request);
        return $this->jsonResponse(json_encode(['success' => true]));
    }
}
