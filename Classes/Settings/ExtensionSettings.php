<?php
declare(strict_types=1);

namespace In2code\In2studyfinder\Settings;

use In2code\In2studyfinder\Utility\PageUtility;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

#[AsAlias(ExtensionSettingsInterface::class)]
class ExtensionSettings implements ExtensionSettingsInterface
{
    public const EXTENSION_KEY = 'in2studyfinder';

    public function __construct(protected readonly ConfigurationManagerInterface $configurationManager)
    {
    }

    public function getTypoScriptSettings(): array
    {
        $typoScriptConfiguration = $this->getFullTypoScriptConfiguration();
        if ($typoScriptConfiguration !== []) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptSetup = $typoScriptService->convertTypoScriptArrayToPlainArray($typoScriptConfiguration);

            return $typoScriptSetup['plugin']['tx_in2studyfinder']['settings'] ?? [];
        }
    }

    public function getConfiguredStoragePids(array $pluginRecord = []): array
    {
        $typoScriptSettings = $this->getTypoScriptSettings();

        $configuration = [
            'storage' => [
                'pids' => $typoScriptSettings['storagePid'] ?? '0',
                'recursive' => $typoScriptSettings['storagePidRecursionLevel'] ?? 1,
            ],
            'settings' => [
                'pids' => $typoScriptSettings['settingsPid'] ?? '0',
                'recursive' => $typoScriptSettings['settingsPidRecursionLevel'] ?? 1,
            ],
        ];

        if (array_key_exists('pages', $pluginRecord) && !empty($pluginRecord['pages'])) {
            $configuration = [
                'storage' => [
                    'pids' => $pluginRecord['pages'],
                    'recursive' => ($pluginRecord['recursive'] ?? '') ? ($this->typoScriptSettings['storagePidRecursionLevel'] ?? 1) : 1,
                ],
            ];
        }

        return $this->getStoragePidsByConfiguration($configuration);
    }

    public static function isEnableGlobalData(): bool
    {
        $extConfigTemplatesSettings = self::getExtConfTemplateSettings();

        return isset($extConfigTemplatesSettings['enableGlobalData']) && $extConfigTemplatesSettings['enableGlobalData'] === '1';
    }

    public static function isCategorisationEnabled(): bool
    {
        $extConfigTemplatesSettings = self::getExtConfTemplateSettings();

        return isset($extConfigTemplatesSettings['enableCategories']) && $extConfigTemplatesSettings['enableCategories'] === '1';
    }

    public static function isPersistentFilterEnabled(): bool
    {
        $extConfigTemplatesSettings = self::getExtConfTemplateSettings();

        return isset($extConfigTemplatesSettings['enablePersistentFilter']) && $extConfigTemplatesSettings['enablePersistentFilter'] === '1';
    }

    protected function getFullTypoScriptConfiguration(): array
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
    }

    /**
     * get the storage pids (incl. recursion levels) from the set plugin record
     */
    protected function getStoragePidsByConfiguration(array $configuration): array
    {
        $storagePids = [];

        foreach ($configuration as $type => $typeConfiguration) {
            $pids = GeneralUtility::intExplode(',', $typeConfiguration['pids']);

            if (array_key_exists('recursive', $typeConfiguration) && $typeConfiguration['recursive'] > 0) {
                $recursiveStoragePids = '';
                foreach ($pids as $pid) {
                    $recursiveStoragePids .=
                        PageUtility::getTreeList($pid, (int)$typeConfiguration['recursive'], 0, 'hidden=0') . ',';
                }

                $storagePids = array_merge($storagePids, GeneralUtility::intExplode(',', $recursiveStoragePids, true));
            }

            $storagePids = array_merge($storagePids, $pids);
        }

        return array_unique($storagePids);
    }

    protected function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    protected static function getExtConfTemplateSettings(): array
    {
        try {
            $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(self::EXTENSION_KEY);
            return ($configuration ?? []);
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException $e) {
            return [];
        }
    }
}
