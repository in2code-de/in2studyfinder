<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class FrontendUtility
{
    public static function getCurrentPageIdentifier(): int
    {
        return $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.page.information')->getId();
    }

    /**
     * @throws AspectNotFoundException
     */
    public static function getCurrentSysLanguageUid(): int
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        return $languageAspect->getId();
    }
}
