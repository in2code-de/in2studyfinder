<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Service\Prompt\PromptInterface;
use In2code\In2studyfinder\Service\FeSessionService;
use Psr\Http\Message\ServerRequestInterface;

class ChatService
{
    protected const SESSION_KEY = 'history';

    protected PromptInterface $prompt;
    protected MistralAdapter $mistralAdapter;
    protected FeSessionService $feSessionService;

    public function __construct(
        MistralAdapter $mistralAdapter,
        PromptInterface $prompt,
        FeSessionService $feSessionService
    ) {
        $this->mistralAdapter = $mistralAdapter;
        $this->feSessionService = $feSessionService;
        $this->prompt = $prompt;
    }

    public function chat(ServerRequestInterface $request, array $pluginSettings): array
    {
        $message = $this->getRequestBody($request)['message'] ?? null;
        if ($message === null) {
            return ['success' => false, 'errorCode' => 1749708782];
        }

        $history = $this->getHistory($request, $pluginSettings);
        $history[] = ['role' => 'user', 'content' => $message];
        $response = $this->mistralAdapter->sendMessage($history, $pluginSettings);
        $this->feSessionService->saveToSession(self::SESSION_KEY, $response['history'], $request);

        return $response;
    }

    public function deleteHistory(ServerRequestInterface $request): void
    {
        $this->feSessionService->saveToSession(self::SESSION_KEY, null, $request);
    }

    protected function getHistory(ServerRequestInterface $request, array $pluginSettings): array
    {
        $history = $this->feSessionService->getFromSession(self::SESSION_KEY, $request);

        if (!is_array($history) || $history === []) {
            $history[] = ['role' => 'system', 'content' => $this->prompt->getLocalizedPrompt($pluginSettings)];
        }

        return $history;
    }

    protected function getRequestBody(ServerRequestInterface $request): array
    {
        return json_decode($request->getBody()->getContents(), true);
    }
}
