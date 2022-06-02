<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

class CacheUtility
{
    /**
     * Adds cache tags to page cache
     *
     * Following cache tags will be added to tsfe:
     * "tx_in2studyfinder_uid_[course:uid]"
     *
     * @param array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public static function addCacheTags($recods): void
    {
        $cacheTags = [];
        foreach ($recods as $record) {
            $uid = $record->getUid();
            $localizedUid = $record->_getProperty('_localizedUid');

            $cacheTags[$uid] = 'tx_in2studyfinder_uid_' . $uid;
            if ($localizedUid) {
                $cacheTags[$localizedUid] = 'tx_in2studyfinder_uid_' . $localizedUid;
            }
        }
        if (count($cacheTags) > 0) {
            $GLOBALS['TSFE']->addCacheTags($cacheTags);
        }
    }
}
