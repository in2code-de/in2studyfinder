<?php

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class VersionUtility extends AbstractUtility
{
    /**
     * Get current TYPO3 version as comparable integer
     *
     * @return int
     */
    public static function getCurrentTypo3MajorVersion()
    {
        $versionArray = VersionNumberUtility::convertVersionStringToArray(TYPO3_version);

        return $versionArray['version_main'];
    }

    /**
     * Is current TYPO3 newer than the version
     *
     * @param int $typo3Version
     * @return bool
     */
    public static function isTypo3MajorVersionAbove($typo3Version)
    {
        return self::getCurrentTypo3MajorVersion() > $typo3Version;
    }

    /**
     * Is current TYPO3 newer than the minium version
     *
     * @param int $typo3Version
     * @return bool
     */
    public static function isTypo3MajorVersionBelow($typo3Version)
    {
        return self::getCurrentTypo3MajorVersion() < $typo3Version;
    }
}
