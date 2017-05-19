<?php

namespace In2code\In2studyfinder\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Sebastian Stein <sebastian.stein@in2code.de>, In2code.de
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AbstractUtility
{

    /**
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return ConfigurationManager
     */
    public static function getConfigurationManager()
    {
        return self::getObjectManager()->get(ConfigurationManager::class);
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     */
    public static function getExtensionConfiguration()
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['in2studyfinder']);
    }
}
