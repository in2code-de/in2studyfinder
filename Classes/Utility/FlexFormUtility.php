<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlexFormUtility
{
    public static function getFlexForm(string $flexFormString, string $key = ''): array
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexForm = $flexFormService->convertFlexFormContentToArray($flexFormString);

        if ($key !== '' && array_key_exists($key, $flexForm)) {
            return $flexForm[$key];
        }

        return $flexForm;
    }
}
