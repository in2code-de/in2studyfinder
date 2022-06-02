<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ExtensionUtility extends AbstractUtility
{
    /**
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getExtensionSettings(string $extKey): array
    {
        return self::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            ucfirst($extKey)
        );
    }

    public static function isIn2studycoursesExtendLoaded(): bool
    {
        $isLoaded = false;

        if (ExtensionManagementUtility::isLoaded('in2studyfinder_extend')) {
            $isLoaded = true;
        }

        return $isLoaded;
    }
}
