<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Service\Prompt\PromptInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Session\UserSession;
use TYPO3\CMS\Core\Session\UserSessionManager;

class ChatService
{
    protected const SESSION_KEY = 'history';

    protected PromptInterface $prompt;
    protected MistralAdapter $mistralAdapter;

    public function __construct(MistralAdapter $mistralAdapter, PromptInterface $prompt) {
        $this->mistralAdapter = $mistralAdapter;
        $this->prompt = $prompt;
    }

    public function chat(ServerRequestInterface $request, array $pluginSettings): array
    {
        $message = $this->getRequestBody($request)['message'] ?? null;
        if ($message === null) {
            return ['success' => false, 'errorCode' => 1749708782];
        }

        $session = $this->getSession($request);
        $history = $this->getHistory($session, $pluginSettings);
        $history[] = ['role' => 'user', 'content' => $message];
        $response = $this->mistralAdapter->sendMessage($history, $pluginSettings);

        $this->updateHistory($session, $response['history']);
        unset($response['history']);
        return $response;
    }

    public function deleteHistory(): void
    {
//        $this->session->set(self::SESSION_KEY, null);
    }

    protected function getHistory(UserSession  $session, array $pluginSettings): array
    {
        $history = $session->get(self::SESSION_KEY);

        if (!is_array($history) || $history === []) {
            $history[] = ['role' => 'system', 'content' => $this->prompt->getLocalizedPrompt($pluginSettings)];
        }

        return $history;
    }

    protected function updateHistory(UserSession $session, array $newMessages): void
    {
        $session->set(self::SESSION_KEY, $newMessages);
        $manager = UserSessionManager::create('FE');
        $manager->fixateAnonymousSession($session);
    }

    protected function getSession(ServerRequestInterface $request): UserSession
    {
        $manager = UserSessionManager::create('FE');
        return $manager->createFromRequestOrAnonymous($request, self::SESSION_KEY);
    }

    protected function getRequestBody(ServerRequestInterface $request): array
    {
        return json_decode($request->getBody()->getContents(), true);
    }
}
