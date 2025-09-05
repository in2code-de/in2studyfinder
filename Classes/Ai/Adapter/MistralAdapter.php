<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Adapter;

use In2code\In2studyfinder\Exception\MissingApiKeyException;
use In2code\In2studyfinder\Exception\ToolNotFoundException;
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
    public function sendMessage(array $messages, array $pluginSettings): array
    {
        $requestBody = [
            'model' => 'mistral-large-latest',
            'messages' => $messages,
            'temperature' => 0.3,
            'max_tokens' => 1000,
            'tools' => $this->getToolConfigurations(),
        ];

        $result = $this->sendRequest($this->getApiUrl(), $requestBody);
        $message = $result['choices'][0]['message'];
        $messages[] = $message;

        if (isset($message['tool_calls'])) {
            $messages = $this->sendMessage(
                $this->buildToolCalls($message, $messages, $pluginSettings),
                $pluginSettings
            );
        }

        return $messages;
    }

    /**
     * @throws ToolNotFoundException
     */
    private function buildToolCalls(array $message, array $messages, array $pluginSettings): array
    {
        foreach ($this->getToolResults($message['tool_calls'], $pluginSettings) as $result) {
            $messages[] = $result;
        }

        return $messages;
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
