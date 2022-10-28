<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @deprecated will be removed in Version 10
 */
class VersionUtility extends AbstractUtility
{
    public static function getCurrentTypo3MajorVersion(): int
    {
        return GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion();
    }

    public static function isTypo3MajorVersionAbove(int $typo3Version): bool
    {
        return self::getCurrentTypo3MajorVersion() > $typo3Version;
    }

    public static function isTypo3MajorVersionBelow(int $typo3Version): bool
    {
        return self::getCurrentTypo3MajorVersion() < $typo3Version;
    }
}
