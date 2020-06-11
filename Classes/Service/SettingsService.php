<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SettingsService extends AbstractService
{
    public function enrichSettingsWithPluginSettings(array $settings, int $pluginUid)
    {
        if (GeneralUtility::_GP('type') === '1308171055' && GeneralUtility::_GP('ce')) {
            $this->settings =
                array_merge_recursive(
                    $this->settings,
                    ExtensionUtility::getFlexFormSettingsByUid(GeneralUtility::_GP('ce'))
                );
        } else {
            $this->logger->error(
                'Incorrect parameters of the Ajax request. Flexform settings could not be set! Maybe the extension\'s layout has been overwritten?',
                ['additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]]
            );
        }
    }
}
