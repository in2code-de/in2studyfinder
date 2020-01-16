<?php

namespace In2code\In2studyfinder\Utility;

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
        return $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['in2studyfinder'];
    }
}
