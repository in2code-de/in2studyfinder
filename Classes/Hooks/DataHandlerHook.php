<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Hooks;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerHook implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Flushes the cache if a studycourse record was edited.
     * This happens on two levels: by UID and by PID.
     */
    public function clearCachePostProc(array $params): void
    {
        if (isset($params['table']) && $params['table'] === StudyCourse::TABLE) {
            $cacheTagsToFlush = [];

            if (isset($params['uid'])) {
                $cacheTagsToFlush[] = 'tx_in2studyfinder_uid_' . $params['uid'];
            }

            if (isset($params['uid_page'])) {
                $cacheTagsToFlush[] = 'tx_in2studyfinder_pid_' . $params['uid_page'];
            }

            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            foreach ($cacheTagsToFlush as $cacheTag) {
                try {
                    $cacheManager->flushCachesInGroupByTag('pages', $cacheTag);
                } catch (NoSuchCacheGroupException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}
