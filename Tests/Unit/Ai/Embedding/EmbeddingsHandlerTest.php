<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Unit\Ai\Embedding;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Embedding\EmbeddingsHandler;
use In2code\In2studyfinder\Ai\Embedding\Repository\EmbeddingRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class EmbeddingsHandlerTest extends UnitTestCase
{
    private MockObject|MistralAdapter $mistralAdapterMock;
    private MockObject|EmbeddingRepository $embeddingRepositoryMock;
    private MockObject|LoggerInterface $loggerMock;
    private EmbeddingsHandler $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mistralAdapterMock = $this->createMock(MistralAdapter::class);
        $this->embeddingRepositoryMock = $this->createMock(EmbeddingRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->subject = new EmbeddingsHandler(
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        );
    }

    #[Test]
    public function createProcessesRecordsAndSavesEmbeddings(): void
    {
        $records = [
            ['uid' => 1, 'title' => 'Test Title 1', 'teaser' => 'Test Teaser 1'],
            ['uid' => 2, 'title' => 'Test Title 2', 'teaser' => 'Test Teaser 2']
        ];
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $embeddingResponse = [
            ['embedding' => [0.1, 0.2, 0.3]],
            ['embedding' => [0.4, 0.5, 0.6]]
        ];

        $this->mistralAdapterMock->expects(self::exactly(2))
            ->method('createEmbedding')
            ->willReturn($embeddingResponse);

        $expectedEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3], 'teaser' => [0.4, 0.5, 0.6]],
            2 => ['uid' => 2, 'title' => [0.1, 0.2, 0.3], 'teaser' => [0.4, 0.5, 0.6]]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($expectedEmbeddings, $tableName);

        $this->subject->create($records, $tableName);
    }

    #[Test]
    public function createHandlesEmptyRecordsArray(): void
    {
        $records = [];
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $this->mistralAdapterMock->expects(self::never())
            ->method('createEmbedding');

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with([], $tableName);

        $this->subject->create($records, $tableName);
    }

    #[Test]
    public function createSkipsRecordsWithNullEmbeddings(): void
    {
        $records = [
            ['uid' => 1, 'title' => 'Test Title 1'],
            ['uid' => 2, 'title' => 'Test Title 2']
        ];
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        // Mock fetchEmbedding to return null for first record and valid embedding for second
        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, ['fetchEmbedding'], [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $subject->expects(self::exactly(2))
            ->method('fetchEmbedding')
            ->willReturnOnConsecutiveCalls(
                null,
                ['uid' => 2, 'title' => [0.1, 0.2, 0.3]]
            );

        $expectedEmbeddings = [
            2 => ['uid' => 2, 'title' => [0.1, 0.2, 0.3]]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($expectedEmbeddings, $tableName);

        $subject->create($records, $tableName);
    }

    #[Test]
    public function addRetrievesExistingEmbeddingsAndAddsNewOne(): void
    {
        $record = ['uid' => 3, 'title' => 'New Title'];
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $existingEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            2 => ['uid' => 2, 'title' => [0.4, 0.5, 0.6]]
        ];

        $newEmbedding = ['uid' => 3, 'title' => [0.7, 0.8, 0.9]];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($existingEmbeddings);

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, ['fetchEmbedding'], [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $subject->expects(self::once())
            ->method('fetchEmbedding')
            ->willReturn($newEmbedding);

        $expectedEmbeddings = $existingEmbeddings;
        $expectedEmbeddings[3] = $newEmbedding;

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($expectedEmbeddings, $tableName);

        $subject->add($record, $tableName);
    }

    #[Test]
    public function addFiltersOutNullEmbeddings(): void
    {
        $record = ['uid' => 3, 'title' => 'New Title'];
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $existingEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            2 => null
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($existingEmbeddings);

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, ['fetchEmbedding'], [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $subject->expects(self::once())
            ->method('fetchEmbedding')
            ->willReturn(['uid' => 3, 'title' => [0.7, 0.8, 0.9]]);

        $expectedEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            3 => ['uid' => 3, 'title' => [0.7, 0.8, 0.9]]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($expectedEmbeddings, $tableName);

        $subject->add($record, $tableName);
    }

    #[Test]
    public function updateRetrievesExistingEmbeddingsAndUpdatesSpecificRecord(): void
    {
        $record = ['uid' => 2, 'title' => 'Updated Title'];
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $existingEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            2 => ['uid' => 2, 'title' => [0.4, 0.5, 0.6]]
        ];

        $updatedEmbedding = ['uid' => 2, 'title' => [0.7, 0.8, 0.9]];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($existingEmbeddings);

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, ['fetchEmbedding'], [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $subject->expects(self::once())
            ->method('fetchEmbedding')
            ->willReturn($updatedEmbedding);

        $expectedEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            2 => $updatedEmbedding
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($expectedEmbeddings, $tableName);

        $subject->update($record, $tableName);
    }

    #[Test]
    public function deleteRemovesSpecificRecordFromEmbeddings(): void
    {
        $uid = 2;
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $existingEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            2 => ['uid' => 2, 'title' => [0.4, 0.5, 0.6]],
            3 => ['uid' => 3, 'title' => [0.7, 0.8, 0.9]]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($existingEmbeddings);

        $expectedEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            3 => ['uid' => 3, 'title' => [0.7, 0.8, 0.9]]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($expectedEmbeddings, $tableName);

        $this->subject->delete($uid, $tableName);
    }

    #[Test]
    public function deleteHandlesNonExistentUid(): void
    {
        $uid = 999;
        $tableName = 'tx_in2studyfinder_domain_model_studycourse';

        $existingEmbeddings = [
            1 => ['uid' => 1, 'title' => [0.1, 0.2, 0.3]],
            2 => ['uid' => 2, 'title' => [0.4, 0.5, 0.6]]
        ];

        $this->embeddingRepositoryMock->expects(self::once())
            ->method('get')
            ->with($tableName)
            ->willReturn($existingEmbeddings);

        // Should save the same embeddings since UID doesn't exist
        $this->embeddingRepositoryMock->expects(self::once())
            ->method('save')
            ->with($existingEmbeddings, $tableName);

        $this->subject->delete($uid, $tableName);
    }

    #[Test]
    public function fetchEmbeddingReturnsCorrectStructure(): void
    {
        $record = ['uid' => 1, 'title' => 'Test Title', 'teaser' => 'Test Teaser'];
        $fieldNames = ['title', 'teaser'];

        $embeddingResponse = [
            ['embedding' => [0.1, 0.2, 0.3]],
            ['embedding' => [0.4, 0.5, 0.6]]
        ];

        $this->mistralAdapterMock->expects(self::once())
            ->method('createEmbedding')
            ->with(['Test Title', 'Test Teaser'])
            ->willReturn($embeddingResponse);

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, null, [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $result = $subject->_call('fetchEmbedding', $record, $fieldNames);

        $expected = [
            'uid' => 1,
            'title' => [0.1, 0.2, 0.3],
            'teaser' => [0.4, 0.5, 0.6]
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function fetchEmbeddingHandlesExceptionAndReturnsNull(): void
    {
        $record = ['uid' => 1, 'title' => 'Test Title'];
        $fieldNames = ['title'];

        $exception = new \Exception('API Error');

        $this->mistralAdapterMock->expects(self::once())
            ->method('createEmbedding')
            ->willThrowException($exception);

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('API Error');

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, null, [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $result = $subject->_call('fetchEmbedding', $record, $fieldNames);

        self::assertNull($result);
    }

    #[Test]
    public function fetchEmbeddingHandlesMissingEmbeddingInResponse(): void
    {
        $record = ['uid' => 1, 'title' => 'Test Title'];
        $fieldNames = ['title'];

        $embeddingResponse = [
            ['no_embedding_key' => [0.1, 0.2, 0.3]]
        ];

        $this->mistralAdapterMock->expects(self::once())
            ->method('createEmbedding')
            ->willReturn($embeddingResponse);

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, null, [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $result = $subject->_call('fetchEmbedding', $record, $fieldNames);

        $expected = [
            'uid' => 1,
            'title' => ''
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function getRecordTextsExtractsCorrectFieldValues(): void
    {
        $record = [
            'uid' => 1,
            'title' => 'Test Title',
            'teaser' => 'Test Teaser',
            'description' => 'Test Description',
            'other_field' => 'Other Value'
        ];
        $fieldNames = ['title', 'teaser', 'description'];

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, null, [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $result = $subject->_call('getRecordTexts', $record, $fieldNames);

        $expected = ['Test Title', 'Test Teaser', 'Test Description'];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function getRecordTextsHandlesMissingFields(): void
    {
        $record = [
            'uid' => 1,
            'title' => 'Test Title'
        ];
        $fieldNames = ['title', 'teaser', 'description'];

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, null, [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $result = $subject->_call('getRecordTexts', $record, $fieldNames);

        $expected = ['Test Title', '', ''];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function getRecordTextsHandlesEmptyFieldNames(): void
    {
        $record = ['uid' => 1, 'title' => 'Test Title'];
        $fieldNames = [];

        $subject = $this->getAccessibleMock(EmbeddingsHandler::class, null, [
            $this->mistralAdapterMock,
            $this->embeddingRepositoryMock,
            $this->loggerMock
        ]);

        $result = $subject->_call('getRecordTexts', $record, $fieldNames);

        self::assertEquals([], $result);
    }
}
