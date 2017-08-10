<?php
namespace In2code\In2studyfinder\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, In2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class VersionUtility extends AbstractUtility
{
    /**
     * Get current TYPO3 version as compareable integer
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
