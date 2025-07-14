<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use In2code\In2studyfinder\Domain\Model\GlobalDataInterface;
use In2code\In2studyfinder\Domain\Repository\GlobalDataRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GlobalDataUtility extends AbstractUtility
{
    public static function existDefaultPreset(GlobalDataRepository $globalDataRepository): bool
    {
        /** @var $globalDataRepository GlobalDataRepository */
        return $globalDataRepository->countDefaultPreset() > 0;
    }

    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public static function getDefaultPreset(
        string $globalDataRepositoryClass = GlobalDataRepository::class
    ): ?GlobalDataInterface {
        $defaultPreset = null;

        $globalDataRepository = GeneralUtility::makeInstance($globalDataRepositoryClass);

        if ($globalDataRepository instanceof GlobalDataRepository && self::existDefaultPreset($globalDataRepository)) {
            return $globalDataRepository->findDefaultPreset();
        }

        return $defaultPreset;
    }
}
