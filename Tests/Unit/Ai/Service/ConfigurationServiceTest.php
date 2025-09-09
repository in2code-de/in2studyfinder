<?php
declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Unit\Ai\Service;

use Exception;
use In2code\In2studyfinder\Ai\Service\ConfigurationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConfigurationServiceTest extends UnitTestCase
{
    private ConfigurationService $subject;
    private ExtensionConfiguration|MockObject $extensionConfigurationMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extensionConfigurationMock = $this->createMock(ExtensionConfiguration::class);
        $this->subject = new ConfigurationService();

        $this->resetSingletonInstances = true;
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test]
    public function getMistralApiKeyReturnsConfiguredKey(): void
    {
        $expectedApiKey = 'test-api-key-12345';

        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'mistralApiKey')
            ->willReturn($expectedApiKey);

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $result = $this->subject->getMistralApiKey();

        self::assertEquals($expectedApiKey, $result);
    }

    #[Test]
    public function getMistralApiKeyReturnsEmptyStringWhenConfigurationFails(): void
    {
        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'mistralApiKey')
            ->willThrowException(new \Exception('Configuration error'));

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $result = $this->subject->getMistralApiKey();

        self::assertEquals('', $result);
    }

    #[Test]
    public function getMistralApiKeyReturnsEmptyStringWhenConfigurationIsNull(): void
    {
        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'mistralApiKey')
            ->willReturn(null);

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $result = $this->subject->getMistralApiKey();

        self::assertEquals('', $result);
    }

    #[Test]
    public function getDetailPidReturnsConfiguredPid(): void
    {
        $expectedPid = 123;

        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'detailPid')
            ->willReturn($expectedPid);

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $result = $this->subject->getDetailPid();

        self::assertEquals($expectedPid, $result);
    }

    public static function invalidPidDataProvider(): array
    {
        return [
            'Empty string' => [''],
            'Zero' => [0],
            'Negative number' => [-1],
            'String' => ['test'],
        ];
    }

    #[Test]
    #[DataProvider('invalidPidDataProvider')]
    public function getDetailPidThrowsExceptionWhenPidIsInvalid($invalidPid): void
    {
        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'detailPid')
            ->willReturn($invalidPid);

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Detail PID not found. Please configure it in the extension settings.');
        $this->expectExceptionCode(1750159320);

        $this->subject->getDetailPid();
    }

    #[Test]
    public function getDetailPidThrowsExceptionWhenConfigurationFails(): void
    {
        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'detailPid')
            ->willThrowException(new \Exception('Configuration error'));

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Detail PID not found. Please configure it in the extension settings.');
        $this->expectExceptionCode(1750159320);

        $this->subject->getDetailPid();
    }

    #[Test]
    public function getExtensionConfigurationHandlesExceptionGracefully(): void
    {
        // Test the private method indirectly through getMistralApiKey
        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'mistralApiKey')
            ->willThrowException(new \RuntimeException('TYPO3 error'));

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $result = $this->subject->getMistralApiKey();

        // Should return empty string when exception occurs
        self::assertEquals('', $result);
    }

    #[Test]
    public function getExtensionConfigurationReturnsNullOnException(): void
    {
        // Test through getDetailPid which should throw exception when null is returned
        $this->extensionConfigurationMock->expects(self::once())
            ->method('get')
            ->with('in2studyfinder', 'detailPid')
            ->willThrowException(new \InvalidArgumentException('Invalid extension key'));

        GeneralUtility::addInstance(ExtensionConfiguration::class, $this->extensionConfigurationMock);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(1750159320);

        $this->subject->getDetailPid();
    }
}
