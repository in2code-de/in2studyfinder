<?php

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Utility\ConfigurationUtility;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ?FrontendInterface $cacheInstance;

    public function __construct()
    {
        if (ConfigurationUtility::isCachingEnabled()) {
            $this->cacheInstance =
                GeneralUtility::makeInstance(CacheManager::class)->getCache('in2studyfinder');
        }
    }
}
