<?php
declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Unit\Ai\Embedding\Service;

use In2code\In2studyfinder\Ai\Embedding\EmbeddingsConfiguration;
use In2code\In2studyfinder\Ai\Embedding\Repository\EmbeddingRepository;
use In2code\In2studyfinder\Ai\Embedding\Service\CosineSimilarityService;
use In2code\In2studyfinder\Exception\InvalidVectorException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CosineSimilarityServiceTest extends UnitTestCase
{
    private CosineSimilarityService $subject;
    private EmbeddingRepository|MockObject $embeddingRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->embeddingRepositoryMock = $this->createMock(EmbeddingRepository::class);
        $this->subject = new CosineSimilarityService($this->embeddingRepositoryMock);
    }

    public static function cosineSimilarityDataProvider(): array
    {
        return [
            'identical vectors' => [
                'vec1' => [1.0, 0.0, 0.0],
                'vec2' => [1.0, 0.0, 0.0],
                'expectException' => false,
                'exceptionClass' => null,
                'exceptionMessage' => null,
                'expectedResult' => 1.0,
                'delta' => 0.0001
            ],
            'orthogonal vectors' => [
                'vec1' => [1.0, 0.0],
                'vec2' => [0.0, 1.0],
                'expectException' => false,
                'exceptionClass' => null,
                'exceptionMessage' => null,
                'expectedResult' => 0.0,
                'delta' => 0.0001
            ],
            'unequal vector lengths' => [
                'vec1' => [1.0, 0.0],
                'vec2' => [1.0, 0.0, 0.0],
                'expectException' => true,
                'exceptionClass' => InvalidVectorException::class,
                'exceptionMessage' => 'Vectors must be equal',
                'expectedResult' => null,
                'delta' => null
            ],
            'zero vectors' => [
                'vec1' => [0.0, 0.0, 0.0],
                'vec2' => [0.0, 0.0, 0.0],
                'expectException' => false,
                'exceptionClass' => null,
                'exceptionMessage' => null,
                'expectedResult' => null, // Will be checked as float
                'delta' => null
            ]
        ];
    }

    #[Test]
    public function getTopNResultsWithSingleVectorReturnsTopResults(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVector = [0.1, 0.2, 0.3];
        $amount = 2;
        $embeddings = [
            1 => [
                'uid' => 1,
                'title' => [0.1, 0.2, 0.3],
                'teaser' => [0.2, 0.3, 0.4],
                'description' => [0.3, 0.4, 0.5]
            ],
            2 => [
                'uid' => 2,
                'title' => [0.9, 0.8, 0.7],
                'teaser' => [0.8, 0.7, 0.6],
                'description' => [0.7, 0.6, 0.5]
            ],
            3 => [
                'uid' => 3,
                'title' => [0.5, 0.5, 0.5],
                'teaser' => [0.4, 0.4, 0.4],
                'description' => [0.6, 0.6, 0.6]
            ]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        $result = $this->subject->getTopNResults($searchVector, $tableName, $amount);

        self::assertCount($amount, $result);
        self::assertArrayHasKey('similarity', $result[0]);
        self::assertArrayHasKey('similarity', $result[1]);
        // Results should be sorted by similarity (highest first)
        self::assertGreaterThanOrEqual($result[1]['similarity'], $result[0]['similarity']);
    }

    #[Test]
    public function getTopNResultsWithMultipleVectorsAveragesSimilarities(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVectors = [
            [0.1, 0.2, 0.3],
            [0.4, 0.5, 0.6]
        ];
        $amount = 1;
        $embeddings = [
            1 => [
                'uid' => 1,
                'title' => [0.1, 0.2, 0.3],
                'teaser' => [0.2, 0.3, 0.4],
                'description' => [0.3, 0.4, 0.5]
            ]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        $result = $this->subject->getTopNResults($searchVectors, $tableName, $amount);

        self::assertCount($amount, $result);
        self::assertArrayHasKey('similarity', $result[0]);
    }

    #[Test]
    public function getTopNResultsWithEmptyEmbeddingsReturnsEmptyArray(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVector = [0.1, 0.2, 0.3];
        $embeddings = [];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        $result = $this->subject->getTopNResults($searchVector, $tableName);

        self::assertEquals([], $result);
    }

    #[Test]
    public function getTopNResultsWithAmountLargerThanEmbeddingsReturnsAllEmbeddings(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVector = [0.1, 0.2, 0.3];
        $amount = 10;
        $embeddings = [
            [
                'uid' => 1,
                'title' => [0.1, 0.2, 0.3],
                'teaser' => [0.2, 0.3, 0.4],
                'description' => [0.3, 0.4, 0.5]
            ],
            [
                'uid' => 2,
                'title' => [0.4, 0.5, 0.6],
                'teaser' => [0.5, 0.6, 0.7],
                'description' => [0.6, 0.7, 0.8]
            ]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        $result = $this->subject->getTopNResults($searchVector, $tableName, $amount);

        self::assertCount(count($embeddings), $result);
    }

    #[Test]
    public function getTopNResultsWithDefaultAmountReturnsThreeResults(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVector = [0.1, 0.2, 0.3];
        $embeddings = [];

        // Create 5 embeddings to test default amount of 3
        for ($i = 1; $i <= 5; $i++) {
            $embeddings[$i] = [
                'uid' => $i,
                'title' => [0.1 * $i, 0.2 * $i, 0.3 * $i],
                'teaser' => [0.2 * $i, 0.3 * $i, 0.4 * $i],
                'description' => [0.3 * $i, 0.4 * $i, 0.5 * $i]
            ];
        }

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        $result = $this->subject->getTopNResults($searchVector, $tableName);

        self::assertCount(3, $result); // Default amount is 3
    }

    #[Test]
    #[DataProvider('cosineSimilarityDataProvider')]
    public function cosineSimilarityCalculatesCorrectly(
        array $vec1,
        array $vec2,
        bool $expectException,
        ?string $exceptionClass,
        ?string $exceptionMessage,
        ?float $expectedResult,
        ?float $delta
    ): void {
        $reflectionClass = new \ReflectionClass(CosineSimilarityService::class);
        $method = $reflectionClass->getMethod('cosineSimilarity');
        $method->setAccessible(true);

        if ($expectException) {
            $this->expectException($exceptionClass);
            $this->expectExceptionMessage($exceptionMessage);
            $this->expectExceptionCode(1750066884);

            $method->invoke($this->subject, $vec1, $vec2);
        } else {
            $result = $method->invoke($this->subject, $vec1, $vec2);

            if ($expectedResult !== null) {
                self::assertEquals($expectedResult, $result, '', $delta);
            } else {
                // For zero vectors case, just check it's a float
                self::assertTrue(is_float($result));
            }
        }
    }

    #[Test]
    public function sortBySimilaritySortsDescending(): void
    {
        $embeddings = [
            ['uid' => 1, 'similarity' => 0.5],
            ['uid' => 2, 'similarity' => 0.9],
            ['uid' => 3, 'similarity' => 0.3],
            ['uid' => 4, 'similarity' => 0.7]
        ];

        $reflectionClass = new \ReflectionClass(CosineSimilarityService::class);
        $method = $reflectionClass->getMethod('sortBySimilarity');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject, $embeddings);

        self::assertEquals(0.9, $result[0]['similarity']);
        self::assertEquals(0.7, $result[1]['similarity']);
        self::assertEquals(0.5, $result[2]['similarity']);
        self::assertEquals(0.3, $result[3]['similarity']);
    }

    #[Test]
    public function sortBySimilarityHandlesMissingSimilarityValues(): void
    {
        $embeddings = [
            ['uid' => 1, 'similarity' => 0.5],
            ['uid' => 2], // missing similarity
            ['uid' => 3, 'similarity' => 0.8]
        ];

        $reflectionClass = new \ReflectionClass(CosineSimilarityService::class);
        $method = $reflectionClass->getMethod('sortBySimilarity');
        $method->setAccessible(true);

        $result = $method->invoke($this->subject, $embeddings);

        self::assertEquals(0.8, $result[0]['similarity']);
        self::assertEquals(0.5, $result[1]['similarity']);
        self::assertEquals(0.0, (float)($result[2]['similarity'] ?? 0)); // Missing similarity treated as 0
    }

    #[Test]
    public function getTopNResultsHandlesEmptySearchVector(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVector = [];
        $embeddings = [
            1 => [
                'uid' => 1,
                'title' => [0.1, 0.2, 0.3],
                'teaser' => [0.2, 0.3, 0.4],
                'description' => [0.3, 0.4, 0.5]
            ]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        // This should handle empty vector gracefully
        $result = $this->subject->getTopNResults($searchVector, $tableName);

        self::assertIsArray($result);
    }

    #[Test]
    public function getTopNResultsWithZeroAmountReturnsEmptyArray(): void
    {
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';
        $searchVector = [0.1, 0.2, 0.3];
        $amount = 0;
        $embeddings = [
            1 => [
                'uid' => 1,
                'title' => [0.1, 0.2, 0.3],
                'teaser' => [0.2, 0.3, 0.4],
                'description' => [0.3, 0.4, 0.5]
            ]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($embeddings);

        $result = $this->subject->getTopNResults($searchVector, $tableName, $amount);

        self::assertEquals([], $result);
    }

}
