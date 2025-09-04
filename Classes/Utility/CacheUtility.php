<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class CacheUtility
{
    public const CACHE_NAME = 'in2studyfinder';

    /**
     * Adds cache tags to page cache
     *
     * Following cache tags will be added to tsfe:
     * "tx_in2studyfinder_uid_[course:uid]"
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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

    public static function flushDetailPageCache(string $cacheTagsToFlush): void
    {
        try {
            GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroupByTag('pages', $cacheTagsToFlush);
        } catch (NoSuchCacheGroupException) {
            // no such cache group
        }
    }

    public static function flushListCaches(string $cacheTagToFlush): void
    {
        try {
            self::getCacheInstance()->flushByTag($cacheTagToFlush);
        } catch (NoSuchCacheGroupException) {
            // no such cache group
        }
    }


    public static function getCacheInstance(): FrontendInterface
    {
        return GeneralUtility::makeInstance(CacheManager::class)->getCache(CacheUtility::CACHE_NAME);
    }

    public static function getCacheIdentifierForStudyCourses(array $options): string
    {
        // create cache Identifier
        $optionsIdentifier = $options === [] ? 'allStudyCourses' : json_encode($options, JSON_THROW_ON_ERROR);

        return md5(
            FrontendUtility::getCurrentPageIdentifier()
            . '-'
            . FrontendUtility::getCurrentSysLanguageUid()
            . '-'
            . $optionsIdentifier
        );
    }
}
