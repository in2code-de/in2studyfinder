<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Embedding;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Embedding\Repository\EmbeddingRepository;
use Psr\Log\LoggerInterface;

class EmbeddingsHandler
{
    protected MistralAdapter $mistralAdapter;
    protected EmbeddingRepository $embeddingRepository;
    protected LoggerInterface $logger;

    public function __construct(
        MistralAdapter $mistralAdapter,
        EmbeddingRepository $embeddingRepository,
        LoggerInterface $logger
    ) {
        $this->mistralAdapter = $mistralAdapter;
        $this->embeddingRepository = $embeddingRepository;
        $this->logger = $logger;
    }

    public function create(array $records, string $tableName): void
    {
        $embeddings = [];
        foreach ($records as $record) {
            $embedding = $this->fetchEmbedding(
                $record,
                $this->getAllowedFields($tableName)
            );
            if ($embedding !== null) {
                $embeddings[$record['uid']] = $embedding;
            }
        }

        $this->embeddingRepository->save($embeddings, $tableName);
    }

    public function add(array $record, string $tableName): void
    {
        $embeddings = $this->embeddingRepository->get($tableName);
        $embeddings[$record['uid']] = $this->fetchEmbedding(
            $record,
            $this->getAllowedFields($tableName)
        );
        $this->embeddingRepository->save(array_filter($embeddings), $tableName);
    }

    public function update(array $record, string $tableName): void
    {
        $embeddings = $this->embeddingRepository->get($tableName);
        $embeddings[$record['uid']] = $this->fetchEmbedding(
            $record,
            $this->getAllowedFields($tableName)
        );
        $this->embeddingRepository->save($embeddings, $tableName);
    }

    public function delete($uid, string $tableName): void
    {
        $embeddings = $this->embeddingRepository->get($tableName);
        unset($embeddings[$uid]);
        $this->embeddingRepository->save($embeddings, $tableName);
    }

    protected function fetchEmbedding(array $record, array $fieldNames): ?array
    {
        try {
            $texts = $this->getRecordTexts($record, $fieldNames);
            $embeddingsResponse = $this->mistralAdapter->createEmbedding($texts);

            $recordEmbedding = ['uid' => $record['uid']];
            foreach ($embeddingsResponse as $index => $embedding) {
                $recordEmbedding[$fieldNames[$index]] = $embedding['embedding'] ?? '';
            }

            return $recordEmbedding;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    protected function getRecordTexts(array $record, array $fieldNames): array
    {
        $texts = [];

        foreach ($fieldNames as $fieldName) {
            $texts[] = $record[$fieldName] ?? '';
        }
        return $texts;
    }

    protected function getAllowedFields(string $tableName): array
    {
        return EmbeddingsConfiguration::getAllowedFields($tableName);
    }
}
