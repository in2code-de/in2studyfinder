<?php

namespace In2code\In2studyfinder\Utility;

class FlexFormUtility extends AbstractUtility
{
    /**
     * @return \TYPO3\CMS\Core\Service\FlexFormService|\TYPO3\CMS\Extbase\Service\FlexFormService
     */
    public static function getFlexFormService()
    {
        // TYPO3 V9 and above
        if (class_exists(\TYPO3\CMS\Core\Service\FlexFormService::class)) {
            $flexFormService = self::getObjectManager()->get(\TYPO3\CMS\Core\Service\FlexFormService::class);
        } else {
            // TYPO3 V8 and below
            $flexFormService = self::getObjectManager()->get(\TYPO3\CMS\Extbase\Service\FlexFormService::class);
        }

        return $flexFormService;
    }
}
