<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\PageTitle\CoursePageTitleProvider;
use In2code\In2studyfinder\Service\FilterService;
use In2code\In2studyfinder\Service\PluginService;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CourseService extends AbstractService
{
    protected array $settings = [];

    protected FilterService $filterService;

    protected PluginService $pluginService;

    protected StudyCourseRepository $studyCourseRepository;

    public function __construct(
        FilterService $filterService,
        StudyCourseRepository $studyCourseRepository,
        PluginService $pluginService
    ) {
        parent::__construct();

        $this->filterService = $filterService;
        $this->studyCourseRepository = $studyCourseRepository;
        $this->pluginService = $pluginService;
    }

    public function findBySearchOptions(array $searchOptions, array $pluginRecord)
    {
        $storagePids = $this->pluginService->getPluginStoragePids($pluginRecord);

        if (!empty($storagePids)) {
            $searchOptions['storagePids'] = $storagePids;
        }

        if (ConfigurationUtility::isCachingEnabled()) {
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

    /**
     * @param array $searchOptions
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function searchAndSortStudyCourses(array $searchOptions)
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

    /**
     * @param array $options
     * @return string
     */
    protected function getCacheIdentifierForStudyCourses($options)
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
