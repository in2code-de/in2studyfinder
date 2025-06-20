<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Adapter;

use GuzzleHttp\Exception\ClientException;
use In2code\In2studyfinder\Ai\Exception\ToolNotFoundException;
use In2code\In2studyfinder\Ai\Service\Prompt\PromptInterface;
use In2code\In2studyfinder\Ai\Service\ToolResultService;
use In2code\In2studyfinder\Ai\Tool\ToolInterface;
use TYPO3\CMS\Core\Http\RequestFactory;

trait AiAdapterTrait
{
    protected string $apiUrl;
    protected string $apiKey;
    protected iterable $tools;
    protected RequestFactory $requestFactory;
    protected PromptInterface $prompt;
    protected ToolResultService $toolResultService;

    private function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    private function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @throws ClientException
     */
    private function sendRequest(string $url, array $requestBody): array
    {
        $response = $this->requestFactory->request(
            $url,
            'POST',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($requestBody)
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getSystemPrompt(array $pluginSettings): string
    {
        return $this->prompt->getLocalizedPrompt($pluginSettings);
    }

    /**
     * @throws ToolNotFoundException
     */
    private function getToolResults(array $toolCalls, array $pluginSettings): array
    {
        return $this->toolResultService->getResultMessages($toolCalls, $pluginSettings);
    }

    private function getToolConfigurations(): array
    {
        $toolConfigurations = [];
        foreach ($this->tools as $tool) {
            if ($tool instanceof ToolInterface) {
                $toolConfigurations[] = $tool->getConfiguration();
            }
        }

        return $toolConfigurations;
    }
}
