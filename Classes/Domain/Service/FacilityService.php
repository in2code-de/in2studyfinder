<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Repository\FacultyRepository;
use In2code\In2studyfinder\Utility\CacheUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;

class FacilityService extends AbstractService
{
    public function __construct(protected FacultyRepository $facultyRepository)
    {
    }

    public function getFacultyCount(array $settings = []): int
    {
        $cacheInstance = CacheUtility::getCacheInstance();
        $cacheIdentifier = md5('facultyCount');
        $facultyCount = $cacheInstance->get($cacheIdentifier);

        if (!$facultyCount) {
            $facultyCount = $this->countFaculties($settings);
            $cacheInstance->set($cacheIdentifier, $facultyCount, ['in2studyfinder']);
        }

        if (!empty($facultyCount)) {
            return $facultyCount;
        }

        return 0;
    }

    private function countFaculties(array $settings): int
    {
        $defaultQuerySettings = GeneralUtility::makeInstance(QuerySettingsInterface::class);

        if (array_key_exists('settingsPid', $settings)) {
            $defaultQuerySettings->setStoragePageIds([$settings['settingsPid']]);
        }

        $this->facultyRepository->setDefaultQuerySettings($defaultQuerySettings);

        return $this->facultyRepository->countAll();
    }
}
