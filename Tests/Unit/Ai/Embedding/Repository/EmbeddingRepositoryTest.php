<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Unit\Ai\Embedding\Repository;

use In2code\In2studyfinder\Ai\Embedding\Repository\EmbeddingRepository;
use In2code\In2studyfinder\Exception\FileNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class EmbeddingRepositoryTest extends UnitTestCase
{
    private MockObject|EmbeddingRepository $subject;
    private string $testDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDirectory = sys_get_temp_dir() . '/embedding_test_' . uniqid();

        $this->subject = $this->getAccessibleMock(EmbeddingRepository::class, ['getPublicPath']);
        $this->subject->method('getPublicPath')
            ->willReturn($this->testDirectory);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->testDirectory);
        parent::tearDown();
    }

    #[Test]
    public function saveCreatesFileWithCorrectContent(): void
    {
        $embeddings = [
            'item1' => [0.1, 0.2, 0.3],
            'item2' => [0.4, 0.5, 0.6]
        ];
        $tableName = 'test_table';

        $this->subject->save($embeddings, $tableName);

        $expectedPath = $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json';

        // Verify file was created and contains correct content
        self::assertFileExists($expectedPath);
        $savedContent = json_decode(file_get_contents($expectedPath), true);
        self::assertEquals($embeddings, $savedContent);
    }

    #[Test]
    public function saveCreatesDirectoryIfNotExists(): void
    {
        $embeddings = ['test' => [1, 2, 3]];
        $tableName = 'test_table';

        $expectedPath = $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json';
        $expectedDir = dirname($expectedPath);

        // Ensure directory doesn't exist initially
        self::assertDirectoryDoesNotExist($expectedDir);

        // Call save method which should create directory
        $this->subject->save($embeddings, $tableName);

        // Verify directory was created
        self::assertDirectoryExists($expectedDir);
        self::assertFileExists($expectedPath);
    }

    #[Test]
    public function saveHandlesEmptyEmbeddingsArray(): void
    {
        $embeddings = [];
        $tableName = 'empty_table';

        $this->subject->save($embeddings, $tableName);

        $expectedPath = $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json';

        self::assertFileExists($expectedPath);
        $savedContent = json_decode(file_get_contents($expectedPath), true);
        self::assertEquals([], $savedContent);
    }

    #[Test]
    public function getReturnsCorrectDataWhenFileExists(): void
    {
        $embeddings = [
            'item1' => [0.1, 0.2, 0.3],
            'item2' => [0.4, 0.5, 0.6]
        ];
        $tableName = 'existing_table';

        // Create test file
        $filePath = $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json';
        $directory = dirname($filePath);
        mkdir($directory, 0777, true);
        file_put_contents($filePath, json_encode($embeddings));

        $result = $this->subject->get($tableName);

        self::assertEquals($embeddings, $result);
    }

    #[Test]
    public function getThrowsFileNotFoundExceptionWhenFileDoesNotExist(): void
    {
        $tableName = 'non_existing_table';

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File ' . $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json not found.');
        $this->expectExceptionCode(1756816437);

        $this->subject->get($tableName);
    }

    #[Test]
    public function getReturnsEmptyArrayWhenFileContainsEmptyJson(): void
    {
        $tableName = 'empty_json_table';

        // Create test file with empty JSON
        $filePath = $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json';
        $directory = dirname($filePath);
        mkdir($directory, 0777, true);
        file_put_contents($filePath, json_encode([]));

        $result = $this->subject->get($tableName);

        self::assertEquals([], $result);
    }

    #[Test]
    public function getHandlesComplexEmbeddingData(): void
    {
        $complexEmbeddings = [
            'document_1' => [
                'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
                'metadata' => ['title' => 'Test Document', 'category' => 'test']
            ],
            'document_2' => [
                'embedding' => [0.6, 0.7, 0.8, 0.9, 1.0],
                'metadata' => ['title' => 'Another Document', 'category' => 'example']
            ]
        ];
        $tableName = 'complex_table';

        // Create test file
        $filePath = $this->testDirectory . EmbeddingRepository::EMBEDDING_JSON_URL . $tableName . '.json';
        $directory = dirname($filePath);
        mkdir($directory, 0777, true);
        file_put_contents($filePath, json_encode($complexEmbeddings));

        $result = $this->subject->get($tableName);

        self::assertEquals($complexEmbeddings, $result);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
