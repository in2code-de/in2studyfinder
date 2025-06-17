<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service\Prompt;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MistralPrompt implements PromptInterface
{
    public function getLocalizedPrompt(array $pluginSettings): string
    {
        $siteLanguage = $this->getSiteLanguage();

        if ($siteLanguage === null) {
            return (string)$pluginSettings['prompt']['default']['value'];
        }

        if (isset($pluginSettings['prompt'][$siteLanguage->getTwoLetterIsoCode()]) === false) {
            return (string)$pluginSettings['prompt']['default']['value'];
        }

        return (string)$pluginSettings['prompt'][$siteLanguage->getTwoLetterIsoCode()]['value'];
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getSiteLanguage(): ?SiteLanguage
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');

        try {
            $langId = $context->getPropertyFromAspect('language', 'id');
        } catch (AspectNotFoundException $e) {
            return null;
        }

        return $site->getLanguageById($langId);
    }
}
