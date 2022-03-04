<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\PageTitle\CoursePageTitleProvider;
use In2code\In2studyfinder\Service\PluginService;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CourseService extends AbstractService
{
    protected array $settings = [];

    protected PluginService $pluginService;

    protected StudyCourseRepository $studyCourseRepository;

    public function __construct(
        StudyCourseRepository $studyCourseRepository,
        PluginService $pluginService
    ) {
        parent::__construct();

        $this->studyCourseRepository = $studyCourseRepository;
        $this->pluginService = $pluginService;
    }

    public function findBySearchOptions(array $searchOptions, array $pluginRecord): array
    {
        $storagePids = $this->pluginService->getPluginStoragePids($pluginRecord);

        if (!empty($storagePids)) {
            $searchOptions['storagePids'] = $storagePids;
        }

        if (!is_null($this->cacheInstance) && ConfigurationUtility::isCachingEnabled()) {
            $cacheIdentifier = $this->getCacheIdentifierForStudyCourses($searchOptions);

            $studyCourses = $this->cacheInstance->get($cacheIdentifier);

            if (!$studyCourses) {
                $studyCourses = $this->searchAndSortStudyCourses($searchOptions);
                $this->cacheInstance->set($cacheIdentifier, $studyCourses, ['in2studyfinder']);
            }
        } else {
            $studyCourses = $this->searchAndSortStudyCourses($searchOptions);
        }

        return $studyCourses;
    }

    public function setPageTitleAndMetadata(StudyCourseInterface $studyCourse): void
    {
        GeneralUtility::makeInstance(CoursePageTitleProvider::class)->setTitle($studyCourse->getDetailPageTitle());
        $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);

        if (!empty($studyCourse->getMetaDescription())) {
            $metaTagManager->getManagerForProperty('description')->addProperty(
                'description',
                $studyCourse->getMetaDescription()
            );
        }
        if (!empty($studyCourse->getMetaKeywords())) {
            $metaTagManager->getManagerForProperty('keywords')->addProperty(
                'description',
                $studyCourse->getMetaKeywords()
            );
        }
    }

    protected function searchAndSortStudyCourses(array $searchOptions): array
    {
        $studyCourses = $this
            ->studyCourseRepository
            ->findAllFilteredByOptions($searchOptions)
            ->toArray();

        if (array_key_exists(0, $studyCourses)) {
            usort($studyCourses, [$studyCourses[0], 'cmpObj']);
        }

        return $studyCourses;
    }

    protected function getCacheIdentifierForStudyCourses(array $options): string
    {
        // create cache Identifier
        if (empty($options)) {
            $optionsIdentifier = 'allStudyCourses';
        } else {
            $optionsIdentifier = json_encode($options);
        }

        return md5(
            FrontendUtility::getCurrentPageIdentifier()
            . '-'
            . FrontendUtility::getCurrentSysLanguageUid()
            . '-'
            . $optionsIdentifier
        );
    }
}
