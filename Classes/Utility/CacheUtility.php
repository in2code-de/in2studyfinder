<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class CacheUtility
{
    /**
     * Adds cache tags to page cache
     *
     * Following cache tags will be added to tsfe:
     * "tx_in2studyfinder_uid_[course:uid]"
     */
    public static function addCacheTags(QueryResultInterface|array $records): void
    {
        $cacheTags = [];
        foreach ($records as $record) {
            $uid = $record->getUid();
            $localizedUid = $record->_getProperty('_localizedUid');

            $cacheTags[$uid] = new CacheTag('tx_in2studyfinder_uid_' . $uid);
            if ($localizedUid) {
                $cacheTags[$localizedUid] = new CacheTag('tx_in2studyfinder_uid_' . $localizedUid);
            }
        }

        if ($cacheTags !== []) {
            $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.cache.collector')->addCacheTags(...$cacheTags);
        }
    }
}
