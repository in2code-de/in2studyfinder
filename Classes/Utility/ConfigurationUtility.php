<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

class ConfigurationUtility extends AbstractUtility
{
    public static function isEnableGlobalData(): bool
    {
        $extensionConfig = AbstractUtility::getExtensionConfiguration();

        return isset($extensionConfig['enableGlobalData']) && $extensionConfig['enableGlobalData'] === '1';
    }

    public static function isCategorisationEnabled(): bool
    {
        $extensionConfig = AbstractUtility::getExtensionConfiguration();

        return isset($extensionConfig['enableCategories']) && $extensionConfig['enableCategories'] === '1';
    }

    public static function isPersistentFilterEnabled(): bool
    {
        $extensionConfig = AbstractUtility::getExtensionConfiguration();

        return isset($extensionConfig['enablePersistentFilter']) && $extensionConfig['enablePersistentFilter'] === '1';
    }

    public static function isBackendModuleEnabled(): bool
    {
        $extensionConfig = AbstractUtility::getExtensionConfiguration();

        return isset($extensionConfig['enableBackendModule']) && $extensionConfig['enableBackendModule'] === '1';
    }
}
