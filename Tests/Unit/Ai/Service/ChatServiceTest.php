<?php
declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Unit\Ai\Service;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use In2code\In2studyfinder\Ai\Service\ChatService;
use In2code\In2studyfinder\Ai\Service\Prompt\PromptInterface;
use In2code\In2studyfinder\Service\FeSessionService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChatServiceTest extends UnitTestCase
{
    private ChatService $subject;
    private MistralAdapter|MockObject $mistralAdapterMock;
    private PromptInterface|MockObject $promptMock;
    private FeSessionService|MockObject $feSessionServiceMock;
    private ServerRequestInterface|MockObject $requestMock;
    private StreamInterface|MockObject $streamMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mistralAdapterMock = $this->createMock(MistralAdapter::class);
        $this->promptMock = $this->createMock(PromptInterface::class);
        $this->feSessionServiceMock = $this->createMock(FeSessionService::class);
        $this->requestMock = $this->createMock(ServerRequestInterface::class);
        $this->streamMock = $this->createMock(StreamInterface::class);

        $this->subject = new ChatService(
            $this->mistralAdapterMock,
            $this->promptMock,
            $this->feSessionServiceMock
        );
    }

    public static function chatExceptionDataProvider(): array
    {
        return [
            'missing message field' => [
                'requestBody' => json_encode(['other' => 'value']),
                'exceptionClass' => InvalidArgumentException::class,
                'exceptionMessage' => 'Message is missing',
                'exceptionCode' => 1749708782
            ],
            'invalid json' => [
                'requestBody' => 'invalid json',
                'exceptionClass' => InvalidArgumentException::class,
                'exceptionMessage' => 'Message is missing',
                'exceptionCode' => null // This test doesn't specify a code
            ]
        ];
    }

    #[Test]
    public function chatWithValidMessageReturnsMessages(): void
    {
        $message = 'Hello, how are you?';
        $pluginSettings = ['setting1' => 'value1'];
        $requestBody = json_encode(['message' => $message]);
        $existingHistory = [['role' => 'system', 'content' => 'System prompt']];
        $expectedHistory = [
            ['role' => 'system', 'content' => 'System prompt'],
            ['role' => 'user', 'content' => $message]
        ];
        $expectedMessages = [
            ['role' => 'system', 'content' => 'System prompt'],
            ['role' => 'user', 'content' => $message],
            ['role' => 'assistant', 'content' => 'Response']
        ];

        $this->streamMock->expects(self::once())
            ->method('getContents')
            ->willReturn($requestBody);

        $this->requestMock->expects(self::once())
            ->method('getBody')
            ->willReturn($this->streamMock);

        $this->feSessionServiceMock->expects(self::once())
            ->method('getFromSession')
            ->with('history', $this->requestMock)
            ->willReturn($existingHistory);

        $this->mistralAdapterMock->expects(self::once())
            ->method('sendMessage')
            ->with($expectedHistory, $pluginSettings)
            ->willReturn($expectedMessages);

        $this->feSessionServiceMock->expects(self::once())
            ->method('saveToSession')
            ->with('history', $expectedMessages, $this->requestMock);

        $result = $this->subject->chat($this->requestMock, $pluginSettings);

        self::assertEquals($expectedMessages, $result);
    }

    #[Test]
    #[DataProvider('chatExceptionDataProvider')]
    public function chatWithInvalidInputThrowsException(
        string $requestBody,
        string $exceptionClass,
        string $exceptionMessage,
        ?int $exceptionCode
    ): void {
        $pluginSettings = ['setting1' => 'value1'];

        $this->streamMock->expects(self::once())
            ->method('getContents')
            ->willReturn($requestBody);

        $this->requestMock->expects(self::once())
            ->method('getBody')
            ->willReturn($this->streamMock);

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        if ($exceptionCode !== null) {
            $this->expectExceptionCode($exceptionCode);
        }

        $this->subject->chat($this->requestMock, $pluginSettings);
    }

    #[Test]
    public function deleteHistoryClearsSession(): void
    {
        $this->feSessionServiceMock->expects(self::once())
            ->method('saveToSession')
            ->with('history', null, $this->requestMock);

        $this->subject->deleteHistory($this->requestMock);
    }

    #[Test]
    public function chatWithNullHistoryCreatesSystemPrompt(): void
    {
        $message = 'Hello';
        $pluginSettings = ['setting1' => 'value1'];
        $requestBody = json_encode(['message' => $message]);
        $systemPrompt = 'System prompt content';

        $this->streamMock->expects(self::once())
            ->method('getContents')
            ->willReturn($requestBody);

        $this->requestMock->expects(self::once())
            ->method('getBody')
            ->willReturn($this->streamMock);

        $this->feSessionServiceMock->expects(self::once())
            ->method('getFromSession')
            ->with('history', $this->requestMock)
            ->willReturn(null);

        $this->promptMock->expects(self::once())
            ->method('getLocalizedPrompt')
            ->with($pluginSettings)
            ->willReturn($systemPrompt);

        $this->mistralAdapterMock->expects(self::once())
            ->method('sendMessage')
            ->willReturn([]);

        $this->feSessionServiceMock->expects(self::once())
            ->method('saveToSession');

        $this->subject->chat($this->requestMock, $pluginSettings);
    }
}
