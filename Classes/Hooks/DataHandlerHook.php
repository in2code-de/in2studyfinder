<?php

namespace In2code\In2studyfinder\Hooks;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerHook
{
    /**
     * Flushes the cache if a studycourse record was edited.
     * This happens on two levels: by UID and by PID.
     *
     * @param array $params
     */
    public function clearCachePostProc(array $params)
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
                $cacheManager->flushCachesInGroupByTag('pages', $cacheTag);
            }
        }
    }
}
