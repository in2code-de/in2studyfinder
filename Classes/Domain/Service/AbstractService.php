<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Utility\ConfigurationUtility;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ?FrontendInterface $cacheInstance;

    protected string $cacheIdentifier = 'in2studyfinder';

    public function __construct()
    {
        if (ConfigurationUtility::isCachingEnabled()) {
            try {
                $this->cacheInstance =
                    GeneralUtility::makeInstance(CacheManager::class)->getCache($this->cacheIdentifier);
            } catch (NoSuchCacheException $exception) {
                $this->logger->error('A cache with identifier "' . $this->cacheIdentifier . '" does not exist.');
            }
        }
    }
}
