<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Repository\FacultyRepository;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;

class FacilityService extends AbstractService
{
    protected FacultyRepository $facultyRepository;

    public function __construct(FacultyRepository $facultyRepository)
    {
        parent::__construct();

        $this->facultyRepository = $facultyRepository;
    }

    public function getFacultyCount(array $settings = []): int
    {
        if (ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = md5('facultyCount');
            $facultyCount = $this->cacheInstance->get($cacheIdentifier);

            if (!$facultyCount) {
                $facultyCount = $this->countFaculties($settings);

                $this->cacheInstance->set($cacheIdentifier, $facultyCount, ['in2studyfinder']);
            }
        } else {
            $facultyCount = $this->countFaculties($settings);
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
