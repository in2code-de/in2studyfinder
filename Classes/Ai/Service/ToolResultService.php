<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use In2code\In2studyfinder\Exception\ToolNotFoundException;
use In2code\In2studyfinder\Ai\Tool\ToolInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class ToolResultService
{
    /**
     * @var iterable<ToolInterface>
     */
    private iterable $tools;
    private LoggerInterface $logger;

    public function __construct(iterable $tools, LoggerInterface $logger)
    {
        $this->tools = $tools;
        $this->logger = $logger;
    }

    /**
     * @throws ToolNotFoundException
     */
    public function getResultMessages(array $toolCalls, array $pluginSettings): array
    {
        $results = [];

        foreach ($toolCalls as $toolCall) {
            $toolName = (string)($toolCall['function']['name'] ?? '');
            $rawArguments = (string)($toolCall['function']['arguments'] ?? '{}');
            $callId = isset($toolCall['id']) ? (string)$toolCall['id'] : null;

            $toolMessage = $this->getToolMessage(
                $this->getToolFromName($toolName),
                json_decode($rawArguments, true),
                $callId,
                $pluginSettings
            );

            if ($toolMessage !== null) {
                $results[] = $toolMessage;
            }
        }

        return $results;
    }

    private function getToolMessage(
        ToolInterface $tool,
        array $arguments,
        ?string $toolCallId,
        array $pluginSettings = []
    ): ?array {
        try {
            return [
                'role' => 'tool',
                'tool_call_id' => $toolCallId,
                'name' => $tool->getName(),
                'content' => json_encode($tool->execute($arguments, $pluginSettings)),
            ];
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            return null;
        }
    }

    /**
     * @throws ToolNotFoundException
     */
    private function getToolFromName(string $toolName): ToolInterface
    {
        foreach ($this->tools as $tool) {
            if ($tool->supports($toolName) === true) {
                return $tool;
            }
        }

        throw new ToolNotFoundException(sprintf('Tool %s not found', $toolName), 1749715418);
    }
}
