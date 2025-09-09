<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Unit\Ai\Service;

use In2code\In2studyfinder\Ai\Service\ToolResultService;
use In2code\In2studyfinder\Ai\Tool\ToolInterface;
use In2code\In2studyfinder\Ai\Tool\Traits\NameTrait;
use In2code\In2studyfinder\Exception\ToolNotFoundException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ToolResultServiceTest extends UnitTestCase
{
    private ToolResultService $subject;
    private LoggerInterface|MockObject $loggerMock;
    private ToolInterface|MockObject $toolMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->toolMock = $this->createMock(ToolInterface::class);
    }

    #[Test]
    public function getResultMessagesReturnsEmptyArrayForEmptyToolCalls(): void
    {
        $this->subject = new ToolResultService([], $this->loggerMock);

        $result = $this->subject->getResultMessages([], []);

        self::assertEquals([], $result);
    }

    #[Test]
    public function getResultMessagesProcessesValidToolCall(): void
    {
        $toolName = 'test-tool';
        $arguments = ['param1' => 'value1'];
        $callId = 'call-123';
        $pluginSettings = ['setting1' => 'value1'];
        $toolResult = ['result' => 'success'];

        $this->toolMock->expects(self::once())
            ->method('supports')
            ->with($toolName)
            ->willReturn(true);

        $this->toolMock->expects(self::once())
            ->method('getName')
            ->willReturn($toolName);

        $this->toolMock->expects(self::once())
            ->method('execute')
            ->with($arguments, $pluginSettings)
            ->willReturn($toolResult);

        $this->subject = new ToolResultService([$this->toolMock], $this->loggerMock);

        $toolCalls = [
            [
                'id' => $callId,
                'function' => [
                    'name' => $toolName,
                    'arguments' => json_encode($arguments)
                ]
            ]
        ];

        $result = $this->subject->getResultMessages($toolCalls, $pluginSettings);

        $expected = [
            [
                'role' => 'tool',
                'tool_call_id' => $callId,
                'name' => $toolName,
                'content' => json_encode($toolResult)
            ]
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function getResultMessagesHandlesMultipleToolCalls(): void
    {
        $tool1Mock = new class implements ToolInterface
        {
            use NameTrait;

            public function getConfiguration(): array
            {
                return [];
            }

            public function execute(array $arguments, array $pluginSettings)
            {
                return ['result' => 'success'];
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $tool2Mock = clone $tool1Mock;
        $tool1Mock->setName('tool1');
        $tool2Mock->setName('tool2');

        $this->subject = new ToolResultService([$tool1Mock, $tool2Mock], $this->loggerMock);

        $toolCalls = [
            [
                'id' => 'call-1',
                'function' => [
                    'name' => 'tool1',
                    'arguments' => '{}'
                ]
            ],
            [
                'id' => 'call-2',
                'function' => [
                    'name' => 'tool2',
                    'arguments' => '{}'
                ]
            ]
        ];

        $result = $this->subject->getResultMessages($toolCalls, []);

        self::assertCount(count($toolCalls), $result);
        self::assertEquals('tool1', $result[0]['name']);
        self::assertEquals('tool2', $result[1]['name']);
    }

    #[Test]
    public function getResultMessagesThrowsExceptionWhenToolNotFound(): void
    {
        $this->toolMock->expects(self::once())
            ->method('supports')
            ->with('unknown-tool')
            ->willReturn(false);

        $this->subject = new ToolResultService([$this->toolMock], $this->loggerMock);

        $toolCalls = [
            [
                'id' => 'call-123',
                'function' => [
                    'name' => 'unknown-tool',
                    'arguments' => '{}'
                ]
            ]
        ];

        $this->expectException(ToolNotFoundException::class);
        $this->expectExceptionMessage('Tool unknown-tool not found');
        $this->expectExceptionCode(1749715418);

        $this->subject->getResultMessages($toolCalls, []);
    }

    #[Test]
    public function getResultMessagesHandlesToolExecutionException(): void
    {
        $exception = new \RuntimeException('Tool execution failed');

        $this->toolMock->expects(self::once())
            ->method('supports')
            ->with('test-tool')
            ->willReturn(true);

        $this->toolMock->expects(self::once())
            ->method('execute')
            ->willThrowException($exception);

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('Tool execution failed');

        $this->subject = new ToolResultService([$this->toolMock], $this->loggerMock);

        $toolCalls = [
            [
                'id' => 'call-123',
                'function' => [
                    'name' => 'test-tool',
                    'arguments' => '{}'
                ]
            ]
        ];

        $result = $this->subject->getResultMessages($toolCalls, []);

        // Should return empty array when tool execution fails
        self::assertEquals([], $result);
    }

    public static function invalidToolCallDataProvider(): array
    {
        return [
            'Missing function key' => [
                [
                    [
                        'id' => 'call-123'
                    ]
                ]
            ],
            'Missing function name' => [
                [
                    [
                        'id' => 'call-123',
                        'function' => []
                    ]
                ]
            ],
            'Empty function name' => [
                [
                    [
                        'id' => 'call-123',
                        'function' => [
                            'name' => ''
                        ]
                    ]
                ]
            ],
            'Missing arguments' => [
                [
                    [
                        'id' => 'call-123',
                        'function' => [
                            'name' => 'test-tool'
                        ]
                    ]
                ]
            ]
        ];
    }

    #[Test]
    #[DataProvider('invalidToolCallDataProvider')]
    public function getResultMessagesHandlesInvalidToolCallStructure(array $toolCalls): void
    {
        $this->toolMock->expects(self::any())
            ->method('supports')
            ->willReturn(false);

        $this->subject = new ToolResultService([$this->toolMock], $this->loggerMock);

        $this->expectException(ToolNotFoundException::class);

        $this->subject->getResultMessages($toolCalls, []);
    }

    #[Test]
    public function getResultMessagesHandlesMissingCallId(): void
    {
        $this->toolMock->expects(self::once())
            ->method('supports')
            ->with('test-tool')
            ->willReturn(true);

        $this->toolMock->expects(self::once())
            ->method('getName')
            ->willReturn('test-tool');

        $this->toolMock->expects(self::once())
            ->method('execute')
            ->willReturn(['result' => 'success']);

        $this->subject = new ToolResultService([$this->toolMock], $this->loggerMock);

        $toolCalls = [
            [
                'function' => [
                    'name' => 'test-tool',
                    'arguments' => '{}'
                ]
            ]
        ];

        $result = $this->subject->getResultMessages($toolCalls, []);

        self::assertCount(1, $result);
        self::assertNull($result[0]['tool_call_id']);
    }

    #[Test]
    public function getResultMessagesFindsCorrectToolFromMultipleTools(): void
    {
        $tool1Mock = $this->createMock(ToolInterface::class);
        $tool2Mock = $this->createMock(ToolInterface::class);

        $tool1Mock->expects(self::once())
            ->method('supports')
            ->with('target-tool')
            ->willReturn(false);

        $tool2Mock->expects(self::once())
            ->method('supports')
            ->with('target-tool')
            ->willReturn(true);

        $tool2Mock->expects(self::once())
            ->method('getName')
            ->willReturn('target-tool');

        $tool2Mock->expects(self::once())
            ->method('execute')
            ->willReturn(['found' => true]);

        $this->subject = new ToolResultService([$tool1Mock, $tool2Mock], $this->loggerMock);

        $toolCalls = [
            [
                'id' => 'call-123',
                'function' => [
                    'name' => 'target-tool',
                    'arguments' => '{}'
                ]
            ]
        ];

        $result = $this->subject->getResultMessages($toolCalls, []);

        self::assertCount(1, $result);
        self::assertEquals('target-tool', $result[0]['name']);
    }
}
