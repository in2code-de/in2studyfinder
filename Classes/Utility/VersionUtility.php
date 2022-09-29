<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class VersionUtility extends AbstractUtility
{
    public static function getCurrentTypo3MajorVersion(): int
    {
        $versionArray = VersionNumberUtility::convertVersionStringToArray(\TYPO3_VERSION);

        return $versionArray['version_main'];
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
