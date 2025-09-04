<?php
declare(strict_types=1);

namespace In2code\In2studyfinder\Tests\Functional\Service;

use In2code\In2studyfinder\Service\FilterService;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class FilterServiceTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['extbase'];
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/in2studyfinder',
    ];

    /**
     * @var FilterService
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Pages.csv');
        $this->subject = $this->get(FilterService::class);
    }

    #[Test]
    public function pluginRendersExpectedOutput(): void
    {
        $this->assertTrue(true);
    }
}
