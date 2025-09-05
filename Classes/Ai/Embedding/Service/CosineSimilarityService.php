<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Embedding\Service;

use In2code\In2studyfinder\Ai\Embedding\EmbeddingsConfiguration;
use In2code\In2studyfinder\Ai\Embedding\Repository\EmbeddingRepository;
use In2code\In2studyfinder\Exception\FileNotFoundException;
use In2code\In2studyfinder\Exception\InvalidVectorException;

class CosineSimilarityService
{
    protected EmbeddingRepository $embeddingRepository;

    public function __construct(EmbeddingRepository $embeddingRepository)
    {
        $this->embeddingRepository = $embeddingRepository;
    }

    /**
     * Get top N results based on cosine similarity with search vectors
     *
     * This method can handle either a single search vector or multiple search vectors.
     * If multiple search vectors are provided, the similarity scores for each vector
     * are calculated separately and then averaged.
     *
     * @param array $searchVectors Single vector or array of vectors
     * @param int $amount Number of results to return
     * @return array Top N results sorted by similarity
     * @throws InvalidVectorException
     * @throws FileNotFoundException
     */
    public function getTopNResults(array $searchVectors, string $tableName, int $amount = 3): array
    {
        $embeddings = $this->embeddingRepository->get($tableName);

        // Check if $searchVectors is a single vector or multiple vectors
        // If the first element is a number, it's a single vector
        $isMultipleVectors = !empty($searchVectors) && !is_numeric(reset($searchVectors));

        // Convert single vector to array of vectors for consistent processing
        $vectorsArray = $isMultipleVectors ? $searchVectors : [$searchVectors];

        foreach ($embeddings as $index => $embedding) {
            $totalSimilarity = 0;
            $vectorCount = count($vectorsArray);

            foreach ($vectorsArray as $searchVector) {
                $fieldSimilarities = 0;
                foreach (EmbeddingsConfiguration::getAllowedFields($tableName) as $field) {
                    $fieldSimilarities += $this->cosineSimilarity($searchVector, $embedding[$field]);
                }

                // Add the average of text and description similarity for this vector
                $totalSimilarity += $fieldSimilarities / count(EmbeddingsConfiguration::getAllowedFields($tableName));
            }

            // Calculate the average similarity across all vectors
            $embeddings[$index]['similarity'] = $vectorCount > 0 ? $totalSimilarity / $vectorCount : 0;
        }

        $sortedEmbeddings = $this->sortBySimilarity($embeddings);
        return array_slice($sortedEmbeddings, 0, $amount);
    }

    /**
     * @throws InvalidVectorException
     */
    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (count($vec1) !== count($vec2)) {
            throw new InvalidVectorException('Vectors must be equal', 1750066884);
        }

        $dot = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vec1 as $index => $vec) {
            $dot += $vec * $vec2[$index];
            $normA += pow($vec, 2);
            $normB += pow($vec2[$index], 2);
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    private function sortBySimilarity(array $embeddings): array
    {
        usort(
            $embeddings,
            function (array $item1, array $item2) {
                $similarity1 = (float)($item1['similarity'] ?? 0);
                $similarity2 = (float)($item2['similarity'] ?? 0);
                return $similarity2 <=> $similarity1;
            }
        );

        return $embeddings;
    }
}
