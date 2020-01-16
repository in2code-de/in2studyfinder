<?php

namespace In2code\In2studyfinder\Utility;

use In2code\In2studyfinder\Domain\Model\TtContent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ExtensionUtility extends AbstractUtility
{
    /**
     * @param string $extKey
     * @return mixed
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getExtensionSettings($extKey)
    {
        return self::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            ucfirst($extKey)
        );
    }

    /**
     * @return bool
     */
    public static function isIn2studycoursesExtendLoaded()
    {
        $isLoaded = false;

        if (ExtensionManagementUtility::isLoaded('in2studyfinder_extend')) {
            $isLoaded = true;
        }

        return $isLoaded;
    }

    /**
     * get the FlexForm Settings for the given content element uid
     *
     * @param $uid integer
     *
     * @return array
     */
    public static function getFlexFormSettingsByUid($uid)
    {
        $record = BackendUtility::getRecord(TtContent::TABLE, $uid);

        $flexFormService = self::getObjectManager()->get(FlexFormService::class);
        $flexFormSettings = $flexFormService->convertFlexFormContentToArray($record['pi_flexform']);

        return $flexFormSettings['settings'];
    }
}
