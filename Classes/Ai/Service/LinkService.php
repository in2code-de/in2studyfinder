<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class LinkService
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly UriBuilder $uriBuilder,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getUrlFromUid(int $uid, string $sysLanguageUid): string
    {
        $this->uriBuilder->setRequest($this->getExtbaseRequest());
        return $this->uriBuilder->reset()
            ->setCreateAbsoluteUri(true)
            ->setLanguage($sysLanguageUid)
            ->setTargetPageUid($this->configurationService->getDetailPid())
            ->uriFor(
                'detail',
                ['studyCourse' => $uid],
                'StudyCourse',
                'In2studyfinder',
                'Detail'
            );
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getExtbaseRequest(): RequestInterface
    {
        $request = $GLOBALS['TYPO3_REQUEST'];

        return new Request(
            $request->withAttribute('extbase', new ExtbaseRequestParameters())
            ->withAttribute('currentContentObject', GeneralUtility::makeInstance(ContentObjectRenderer::class)),
        );
    }
}
