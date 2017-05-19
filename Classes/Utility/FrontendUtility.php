<?php
namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class FrontendUtility
 */
class FrontendUtility
{

    /**
     * Get current page identifier
     *
     * @return int
     */
    public static function getCurrentPageIdentifier()
    {
        return (int)self::getTyposcriptFrontendController()->id;
    }

    /**
     * Get current language uid
     *
     * @return int
     */
    public static function getCurrentSysLanguageUid()
    {
        return (int)self::getTyposcriptFrontendController()->sys_language_uid;
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
