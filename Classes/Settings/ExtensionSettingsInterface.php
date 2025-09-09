<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Settings;

interface ExtensionSettingsInterface
{
    /**
     * the extension typoscript settings
     */
    public function getTypoScriptSettings(): array;

    /**
     * returns the configured storage pids (storagePid, settingsPid) incl. recursion level.
     *
     * pluginRecord (if set) overrides the settings from the typoscript configuration
     */
    public function getConfiguredStoragePids(array $pluginRecord = []): array;

    public static function isEnableGlobalData(): bool;
    public static function isCategorisationEnabled(): bool;
    public static function isPersistentFilterEnabled(): bool;
}
