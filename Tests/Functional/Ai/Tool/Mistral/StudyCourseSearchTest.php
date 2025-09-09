<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Functional\Ai\Tool\Mistral;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Embedding\Service\CosineSimilarityService;
use In2code\In2studyfinder\Ai\Service\LinkService;
use In2code\In2studyfinder\Ai\Tool\Mistral\StudyCourseSearch;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class StudyCourseSearchTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['extbase'];
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/in2studyfinder',
    ];

    private StudyCourseSearch $subject;
    private CosineSimilarityService|MockObject $cosineSimilarityServiceMock;
    private LinkService|MockObject $linkServiceMock;
    private MistralAdapter|MockObject $mistralAdapterMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/Pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/StudyCourses.csv');

        // Create mocks for dependencies
        $this->cosineSimilarityServiceMock = $this->createMock(CosineSimilarityService::class);
        $this->linkServiceMock = $this->createMock(LinkService::class);
        $this->mistralAdapterMock = $this->createMock(MistralAdapter::class);

        // Create subject with mocked dependencies
        $this->subject = new StudyCourseSearch(
            $this->cosineSimilarityServiceMock,
            $this->linkServiceMock,
            $this->mistralAdapterMock
        );
    }

    #[Test]
    public function getConfigurationReturnsExpectedStructure(): void
    {
        $configuration = $this->subject->getConfiguration();

        $this->assertIsArray($configuration);
        $this->assertEquals('function', $configuration['type']);
        $this->assertEquals('search_study_program_urls', $configuration['function']['name']);
        $this->assertStringContainsString('Studiengängen', $configuration['function']['description']);
        $this->assertEquals('object', $configuration['function']['parameters']['type']);
        $this->assertArrayHasKey('search_terms', $configuration['function']['parameters']['properties']);
        $this->assertEquals(['search_terms'], $configuration['function']['parameters']['required']);
    }

    #[Test]
    public function executeReturnsStudyCoursesWithUrls(): void
    {
        // Arrange
        $searchTerms = ['Computer Science', 'Engineering'];
        $arguments = ['search_terms' => $searchTerms];
        $pluginSettings = ['topNResults' => 2];

        $mockVectors = [
            [0.1, 0.2, 0.3],
            [0.4, 0.5, 0.6]
        ];

        $mockEmbeddings = [
            ['embedding' => $mockVectors[0]],
            ['embedding' => $mockVectors[1]]
        ];

        $mockStudyCourseVectors = [
            ['uid' => 1, 'similarity' => 0.95],
            ['uid' => 2, 'similarity' => 0.85]
        ];

        // Mock MistralAdapter
        $this->mistralAdapterMock
            ->expects($this->once())
            ->method('createEmbedding')
            ->with($searchTerms)
            ->willReturn($mockEmbeddings);

        // Mock CosineSimilarityService
        $this->cosineSimilarityServiceMock
            ->expects($this->once())
            ->method('getTopNResults')
            ->with($mockVectors, StudyCourse::TABLE, 2)
            ->willReturn($mockStudyCourseVectors);

        // Mock LinkService
        $this->linkServiceMock
            ->expects($this->exactly(2))
            ->method('getUrlFromUid')
            ->willReturnCallback(function ($uid, $language) {
                return "https://example.com/study-course/{$uid}?lang={$language}";
            });

        // Act
        $result = $this->subject->execute($arguments, $pluginSettings);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        foreach ($result as $studyCourse) {
            $this->assertArrayHasKey('uid', $studyCourse);
            $this->assertArrayHasKey('title', $studyCourse);
            $this->assertArrayHasKey('description', $studyCourse);
            $this->assertArrayHasKey('sys_language_uid', $studyCourse);
            $this->assertArrayHasKey('url', $studyCourse);
            $this->assertStringStartsWith('https://example.com/study-course/', $studyCourse['url']);
        }
    }

    #[Test]
    public function executeHandlesEmptySearchTerms(): void
    {
        // Arrange
        $arguments = ['search_terms' => []];
        $pluginSettings = ['topNResults' => 3];

        // Mock MistralAdapter
        $this->mistralAdapterMock
            ->expects($this->never())
            ->method('createEmbedding');

        // Mock CosineSimilarityService
        $this->cosineSimilarityServiceMock
            ->expects($this->once())
            ->method('getTopNResults')
            ->with([], StudyCourse::TABLE, 3)
            ->willReturn([]);

        // Act
        $result = $this->subject->execute($arguments, $pluginSettings);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function executeUsesDefaultTopNResultsWhenNotProvided(): void
    {
        // Arrange
        $searchTerms = ['Mathematics'];
        $arguments = ['search_terms' => $searchTerms];
        $pluginSettings = []; // No topNResults provided

        $mockVectors = [[0.1, 0.2, 0.3]];
        $mockEmbeddings = [['embedding' => $mockVectors[0]]];

        // Mock MistralAdapter
        $this->mistralAdapterMock
            ->expects($this->once())
            ->method('createEmbedding')
            ->with($searchTerms)
            ->willReturn($mockEmbeddings);

        // Mock CosineSimilarityService - should use default value of 3
        $this->cosineSimilarityServiceMock
            ->expects($this->once())
            ->method('getTopNResults')
            ->with($mockVectors, StudyCourse::TABLE, 3)
            ->willReturn([]);

        // Act
        $this->subject->execute($arguments, $pluginSettings);
    }

    #[Test]
    public function executeHandlesSingleSearchTerm(): void
    {
        // Arrange
        $searchTerms = ['Physics'];
        $arguments = ['search_terms' => $searchTerms];
        $pluginSettings = ['topNResults' => 1];

        $mockVectors = [[0.7, 0.8, 0.9]];
        $mockEmbeddings = [['embedding' => $mockVectors[0]]];
        $mockStudyCourseVectors = [['uid' => 3, 'similarity' => 0.92]];

        // Mock MistralAdapter
        $this->mistralAdapterMock
            ->expects($this->once())
            ->method('createEmbedding')
            ->with($searchTerms)
            ->willReturn($mockEmbeddings);

        // Mock CosineSimilarityService
        $this->cosineSimilarityServiceMock
            ->expects($this->once())
            ->method('getTopNResults')
            ->with($mockVectors, StudyCourse::TABLE, 1)
            ->willReturn($mockStudyCourseVectors);

        // Mock LinkService
        $this->linkServiceMock
            ->expects($this->once())
            ->method('getUrlFromUid')
            ->with(3, '0')
            ->willReturn('https://example.com/study-course/3?lang=0');

        // Act
        $result = $this->subject->execute($arguments, $pluginSettings);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function executeHandlesLanguageSpecificStudyCourses(): void
    {
        // Arrange
        $searchTerms = ['German Course'];
        $arguments = ['search_terms' => $searchTerms];
        $pluginSettings = ['topNResults' => 1];

        $mockVectors = [[0.1, 0.2, 0.3]];
        $mockEmbeddings = [['embedding' => $mockVectors[0]]];
        $mockStudyCourseVectors = [['uid' => 4, 'similarity' => 0.88]];

        // Mock MistralAdapter
        $this->mistralAdapterMock
            ->expects($this->once())
            ->method('createEmbedding')
            ->with($searchTerms)
            ->willReturn($mockEmbeddings);

        // Mock CosineSimilarityService
        $this->cosineSimilarityServiceMock
            ->expects($this->once())
            ->method('getTopNResults')
            ->with($mockVectors, StudyCourse::TABLE, 1)
            ->willReturn($mockStudyCourseVectors);

        // Mock LinkService for German language
        $this->linkServiceMock
            ->expects($this->once())
            ->method('getUrlFromUid')
            ->with(4, '1')
            ->willReturn('https://example.com/study-course/4?lang=1');

        // Act
        $result = $this->subject->execute($arguments, $pluginSettings);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('https://example.com/study-course/4?lang=1', $result[0]['url']);
    }
}
