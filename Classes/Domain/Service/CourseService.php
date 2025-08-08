<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\PageTitle\CoursePageTitleProvider;
use In2code\In2studyfinder\Utility\CacheUtility;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CourseService extends AbstractService
{
    protected array $settings = [];

    public function __construct(
        protected StudyCourseRepository $studyCourseRepository,
    ) {}

    public function findBySearchOptions(array $searchOptions, array $pluginRecord): array
    {
        $cacheInstance = CacheUtility::getCacheInstance();
        $cacheIdentifier = CacheUtility::getCacheIdentifierForStudyCourses($searchOptions);
        $studyCourses = $cacheInstance->get($cacheIdentifier);

        if (!$studyCourses) {
            $studyCourses = $this->studyCourseRepository->findAllFilteredByOptions($searchOptions);
            $cacheInstance->set($cacheIdentifier, $studyCourses, ['in2studyfinder']);
        }

        return $studyCourses;
    }

    public function setPageTitleAndMetadata(StudyCourse $studyCourse): void
    {
        GeneralUtility::makeInstance(CoursePageTitleProvider::class)->setTitle($studyCourse->getDetailPageTitle());
        $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);

        if ($studyCourse->getMetaDescription() !== '') {
            $metaTagManager->getManagerForProperty('description')->addProperty(
                'description',
                $studyCourse->getMetaDescription()
            );
        }
        if ($studyCourse->getMetaKeywords() !== '') {
            $metaTagManager->getManagerForProperty('keywords')->addProperty(
                'keywords',
                $studyCourse->getMetaKeywords()
            );
        }
    }
}
