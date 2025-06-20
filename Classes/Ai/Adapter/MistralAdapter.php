<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Adapter;

use In2code\In2studyfinder\Ai\Exception\MissingApiKeyException;
use In2code\In2studyfinder\Ai\Exception\ToolNotFoundException;
use In2code\In2studyfinder\Ai\Service\ConfigurationService;
use In2code\In2studyfinder\Ai\Service\Prompt\PromptInterface;
use In2code\In2studyfinder\Ai\Service\ToolResultService;
use TYPO3\CMS\Core\Http\RequestFactory;

class MistralAdapter implements AIAdapterInterface
{
    use AiAdapterTrait;

    private string $embeddingApiUrl;

    /**
     * @throws MissingApiKeyException
     */
    public function __construct(
        iterable $tools,
        RequestFactory $requestFactory,
        PromptInterface $prompt,
        ToolResultService $toolResultService,
        ConfigurationService $configurationService
    ) {
        $this->apiUrl = 'https://api.mistral.ai/v1/chat/completions';
        $this->embeddingApiUrl = 'https://api.mistral.ai/v1/embeddings';
        $this->apiKey = $configurationService->getMistralApiKey();

        $this->toolResultService = $toolResultService;
        $this->requestFactory = $requestFactory;
        $this->prompt = $prompt;
        $this->tools = $tools;
    }

    /**
     * @throws ToolNotFoundException
     */
    public function sendMessage(string $message, array $pluginSettings): array
    {
        $requestBody = [
            'model' => 'mistral-large-latest',
            'messages' => [
                ['role' => 'system', 'content' => $this->getSystemPrompt($pluginSettings)],
                ['role' => 'user', 'content' => $message]
            ],
            'temperature' => 0.3,
            'max_tokens' => 1000,
            'tools' => $this->getToolConfigurations(),
        ];

        $result = $this->sendRequest($this->getApiUrl(), $requestBody);

        if (isset($result['choices'][0]['message']['tool_calls'])) {
            return $this->sendFollowUpMessage($result['choices'][0]['message']['tool_calls'], $message, $pluginSettings);
        }

        return [
            'success' => true,
            'message' => $result['choices'][0]['message']['content'] ?? 'Keine Antwort erhalten'
        ];
    }

    /**
     * @throws ToolNotFoundException
     */
    private function sendFollowUpMessage(array $toolCalls, string $originalMessage, array $pluginSettings): array
    {
        $messages = [
            ['role' => 'user', 'content' => $originalMessage],
            ['role' => 'assistant', 'content' => null, 'tool_calls' => $toolCalls]
        ];

        foreach ($this->getToolResults($toolCalls, $pluginSettings) as $result) {
            $messages[] = $result;
        }

        $requestBody = [
            'model' => 'mistral-large-latest',
            'messages' => $messages,
            'temperature' => 0.3,
            'max_tokens' => 1000
        ];

        $result = $this->sendRequest($this->getApiUrl(), $requestBody);

        return [
            'success' => true,
            'message' => $result['choices'][0]['message']['content'] ?? 'Keine Antwort erhalten'
        ];
    }


    public function createEmbedding(array $texts): array
    {
        $requestBody = [
            'model' => 'mistral-embed',
            'input' => $texts,
        ];

        $result = $this->sendRequest($this->embeddingApiUrl, $requestBody);

        if (isset($result['data'])) {
            return (array)$result['data'];
        }

        return [];
    }
}
