<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Embedding\Repository;

use In2code\In2studyfinder\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EmbeddingRepository
{
    public const EMBEDDING_JSON_URL = '/fileadmin/in2studyfinder/chatbot/embeddings/';

    public function save(array $embeddings, string $tableName): void
    {
        $jsonFilePath = Environment::getPublicPath() . self::EMBEDDING_JSON_URL . $tableName . '.json';
        $directory = dirname($jsonFilePath);
        if (!is_dir($directory)) {
            GeneralUtility::mkdir_deep($directory);
        }

        file_put_contents($jsonFilePath, json_encode($embeddings, JSON_PRETTY_PRINT));
    }

    /**
     * @throws FileNotFoundException
     */
    public function get(string $tableName): array
    {
        $jsonFilePath = Environment::getPublicPath() . self::EMBEDDING_JSON_URL . $tableName . '.json';
        if (file_exists($jsonFilePath)) {
            return json_decode(file_get_contents($jsonFilePath), true);
        }

        throw new FileNotFoundException('File ' . $jsonFilePath . ' not found.', 1756816437);
    }
}
