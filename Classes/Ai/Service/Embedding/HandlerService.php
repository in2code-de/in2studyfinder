<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service\Embedding;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class HandlerService
{
    public const EMBEDDING_JSON_URL = '/fileadmin/in2studyfinder/chatbot/embeddings/';

    protected MistralAdapter $mistralAdapter;
    protected LoggerInterface $logger;

    public function __construct(MistralAdapter $mistralAdapter, LoggerInterface $logger)
    {
        $this->mistralAdapter = $mistralAdapter;
        $this->logger = $logger;
    }

    public function create(array $records, string $tableName, array $fieldNames): void
    {
        $embeddings = [];

        foreach ($records as $record) {
            try {
                $texts = $this->getRecordTexts($record, $fieldNames);
                $embeddingsResponse = $this->mistralAdapter->createEmbedding($texts);

                $recordEmbedding = ['uid' => $record['uid']];
                foreach ($embeddingsResponse as $index => $embedding) {
                    $recordEmbedding[$fieldNames[$index]] = $embedding['embedding'] ?? '';
                }

                $embeddings[$record['uid']] = $recordEmbedding;
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $this->saveEmbeddingsToFile($embeddings, $tableName);
    }

    protected function getRecordTexts(array $record, array $fieldNames): array
    {
        $texts = [];

        foreach ($fieldNames as $fieldName) {
            $texts[] = $record[$fieldName] ?? '';
        }
        return $texts;
    }

    protected function saveEmbeddingsToFile(array $embeddings, string $tableName): void
    {
        $jsonFilePath = Environment::getPublicPath() . self::EMBEDDING_JSON_URL . $tableName . '.json';
        $directory = dirname($jsonFilePath);
        if (!is_dir($directory)) {
            GeneralUtility::mkdir_deep($directory);
        }

        file_put_contents($jsonFilePath, json_encode($embeddings, JSON_PRETTY_PRINT));
    }
}
