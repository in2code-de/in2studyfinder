<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class LinkService
{
    private ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    /**
     * @throws Exception
     */
    public function getUrlFromUid(int $uid, string $sysLanguageUid): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return $uriBuilder->setCreateAbsoluteUri(true)
            ->setLanguage($sysLanguageUid)
            ->setTargetPageUid($this->configurationService->getDetailPid())
            ->uriFor(
                'detail',
                ['studyCourse' => $uid],
                'StudyCourse',
                'In2studyfinder',
                'Pi2'
            );
    }
}
