<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ExtensionUtility extends AbstractUtility
{
    public static function getExtensionSettings(string $extKey): array
    {
        return self::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            ucfirst($extKey)
        );
    }
}
