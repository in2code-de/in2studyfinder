<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use GuzzleHttp\Exception\ClientException;
use In2code\In2studyfinder\Ai\Service\ChatService;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ChatController extends ActionController
{
    private ChatService $chatService;
    private LoggerInterface $logger;

    public function __construct(ChatService $chatService, LoggerInterface $logger) {
        $this->chatService = $chatService;
        $this->logger = $logger;
    }

    public function indexAction(): void
    {
    }

    public function chatAction(): string
    {
        try {
            return json_encode($this->chatService->chat($this->request, $this->settings));
        } catch (ClientException $exception) {
            $this->logger->error($exception->getResponse()->getBody()->getContents());
            return json_encode(['success' => false, 'errorCode' => $exception->getCode()]);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return json_encode(['success' => false, 'errorCode' => $throwable->getCode()]);
        }
    }
}
