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
        $data = $this->request->getAttribute('currentContentObject')->data;
        $this->chatService->deleteHistory($this->request, (int)($data['uid'] ?? 0));
        $this->view->assignMultiple([
            'data' => $data,
        ]);
        return $this->htmlResponse();
    }

    public function chatAction(int $uid): ResponseInterface
    {
        try {
            $messages = $this->chatService->chat($this->request, $this->settings, $uid);
            $this->historyLogService->logHistory($messages, $this->request, $uid);
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

    public function deleteHistoryAction(int $uid): ResponseInterface
    {
        $this->chatService->deleteHistory($this->request, $uid);
        return $this->redirect('index');
    }
}
