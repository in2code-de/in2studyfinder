<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Tool\Mistral;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Embedding\Service\CosineSimilarityService;
use In2code\In2studyfinder\Ai\Exception\FileNotFoundException;
use In2code\In2studyfinder\Ai\Exception\InvalidVectorException;
use In2code\In2studyfinder\Ai\Exception\MissingArgumentException;
use In2code\In2studyfinder\Ai\Service\LinkService;
use In2code\In2studyfinder\Ai\Tool\ToolInterface;
use In2code\In2studyfinder\Ai\Tool\Traits\GetArgumentTrait;
use In2code\In2studyfinder\Ai\Tool\Traits\NameTrait;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StudyCourseSearch implements ToolInterface
{
    use NameTrait;
    use GetArgumentTrait;

    private LinkService $linkService;
    private CosineSimilarityService $cosineSimilarityService;
    private MistralAdapter $mistralAdapter;

    public function __construct(
        CosineSimilarityService $cosineSimilarityService,
        LinkService $linkService,
        MistralAdapter $mistralAdapter
    ) {
        $this->name = 'search_study_program_urls';
        $this->linkService = $linkService;
        $this->cosineSimilarityService = $cosineSimilarityService;
        $this->mistralAdapter = $mistralAdapter;
    }

    public function getConfiguration(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name,
                'description' => 'Gibt Studiengängen anhand mehrerer Suchbegriffe zurück.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'search_terms' => [
                            'type' => 'array',
                            'description' => 'Ein array mit mehreren Suchbegriffen als string.',
                            'items' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                    'required' => ['search_terms'],
                ],
            ],
        ];
    }

    /**
     * @throws DBALException
     * @throws Exception
     * @throws MissingArgumentException
     * @throws FileNotFoundException
     * @throws InvalidVectorException
     * @throws \Exception
     */
    public function execute(array $arguments, array $pluginSettings)
    {
        // Get search terms from arguments
        $searchTerms = (array)$this->getArgument('search_terms', $arguments);

        // Get combined vector for all search terms
        $searchVector = $this->getSearchTermsAsVectors($searchTerms);

        $amount = (int)($pluginSettings['topNResults'] ?? 3);
        $studyCourses = $this->cosineSimilarityService->getTopNResults($searchVector, StudyCourse::TABLE, $amount);

        // Query database for study course details
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(StudyCourse::TABLE);

        $studyCourses = $queryBuilder->select('uid', 'title', 'description', 'sys_language_uid')
            ->from(StudyCourse::TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    array_column($studyCourses, 'uid')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        // Add URLs to study courses
        foreach ($studyCourses as $index => $studyCourse) {
            $studyCourse['url'] = $this->linkService->getUrlFromUid(
                (int)($studyCourse['uid'] ?? 0),
                (string)($studyCourse['sys_language_uid'] ?? 0),
            );
            $studyCourses[$index] = $studyCourse;
        }

        return $studyCourses;
    }

    /**
     * Converts multiple search terms to vectors
     *
     * This method takes an array of search terms and converts each term to a vector using the Mistral API.
     * It returns an array of vectors, where each vector represents the semantic meaning of a search term.
     *
     * The CosineSimilarityService will handle multiple vectors by calculating the similarity for each vector
     * separately and then averaging the results.
     *
     * @param array $searchTerms Array of search terms
     * @return array Array of vector representations, or a single vector if only one search term
     */
    private function getSearchTermsAsVectors(array $searchTerms): array
    {
        // If no search terms provided, return empty array
        if (empty($searchTerms)) {
            return [];
        }

        // Get embeddings for all search terms
        $embeddings = $this->mistralAdapter->createEmbedding($searchTerms);

        // Extract just the embedding vectors from the response
        $vectors = [];
        foreach ($embeddings as $embedding) {
            $vectors[] = $embedding['embedding'];
        }

        return $vectors;
    }
}
