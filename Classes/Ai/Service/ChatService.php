<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Service\Prompt\PromptInterface;
use In2code\In2studyfinder\Service\FeSessionService;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

class ChatService
{
    protected const SESSION_KEY = 'history';

    public function __construct(
        protected MistralAdapter $mistralAdapter,
        protected PromptInterface $prompt,
        protected FeSessionService $feSessionService
    ) {
    }

    public function chat(ServerRequestInterface $request, array $pluginSettings): array
    {
        $message = $this->getRequestBody($request)['message'] ?? throw new InvalidArgumentException('Message is missing', 1749708782);

        $history = $this->getHistory($request, $pluginSettings);
        $history[] = ['role' => 'user', 'content' => $message];

        $messages = $this->mistralAdapter->sendMessage($history, $pluginSettings);
        $this->feSessionService->saveToSession(self::SESSION_KEY, $messages, $request);

        return $messages;
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
        $decoded = json_decode($request->getBody()->getContents(), true);
        return is_array($decoded) ? $decoded : [];
    }
}
