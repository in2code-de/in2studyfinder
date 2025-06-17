<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Controller;

use GuzzleHttp\Exception\ClientException;
use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use Psr\Log\LoggerInterface;
use Throwable;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ChatController extends ActionController
{
    private MistralAdapter $mistralAdapter;
    private LoggerInterface $logger;

    public function __construct(
        MistralAdapter $mistralAdapter,
        LoggerInterface $logger
    ) {
        $this->mistralAdapter = $mistralAdapter;
        $this->logger = $logger;
    }

    public function indexAction(): void
    {
    }

    public function chatAction(): string
    {
        $requestBody = json_decode($this->request->getBody()->getContents(), true);
        $message =  isset($requestBody['message']) ? (string)$requestBody['message'] : null;

        if ($message === null) {
            return json_encode(['success' => false, 'errorCode' => 1749708782]);
        }

        try {
            return json_encode($this->mistralAdapter->sendMessage($message, $this->settings));
        } catch (ClientException $exception) {
            $this->logger->error($exception->getResponse()->getBody()->getContents());
            return json_encode(['success' => false, 'errorCode' => $exception->getCode()]);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return json_encode(['success' => false, 'errorCode' => $throwable->getCode()]);
        }
    }
}
