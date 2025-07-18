<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\PageTitle\CoursePageTitleProvider;
use In2code\In2studyfinder\Service\PluginService;
use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ClassSchema\Property;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CourseService extends AbstractService
{
    protected array $settings = [];

    protected PluginService $pluginService;

    protected StudyCourseRepository $studyCourseRepository;

    /**
     * @var ReflectionService
     */
    protected $reflectionService;

    public function __construct(
        StudyCourseRepository $studyCourseRepository,
        PluginService $pluginService
    ) {
        parent::__construct();

        $this->studyCourseRepository = $studyCourseRepository;
        $this->pluginService = $pluginService;
        $this->reflectionService = GeneralUtility::makeInstance(ReflectionService::class);
    }

    public function findBySearchOptions(array $searchOptions, array $pluginRecord): array
    {
        $storagePids = $this->pluginService->getPluginStoragePids($pluginRecord);

        if (!empty($storagePids)) {
            $searchOptions['storagePids'] = $storagePids;
        }

        if (ConfigurationUtility::isCachingEnabled() && !is_null($this->cacheInstance)) {
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
                'keywords',
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

    public function getCourseProperties(StudyCourse $course, array $excludedFields = []): array
    {
        return $this->getCoursePropertyList(
            $this->reflectionService->getClassSchema($course)->getProperties(),
            $excludedFields
        );
    }

    /**
     * @param Property[] $objectProperties
     */
    private function getCoursePropertyList(
        array $objectProperties,
        array $excludedFields
    ): array {
        $propertyArray = [];

        foreach ($objectProperties as $property) {
            if (
                !in_array(
                    $property->getName(),
                    $excludedFields,
                    true
                )
            ) {
                $elementType = $property->getElementType();
                $type = $property->getType();

                if ($type === ObjectStorage::class) {
                    if (class_exists($elementType)) {
                        $propertyArray[$property->getName()] = $this->getCoursePropertyList(
                            $this->reflectionService->getClassSchema($elementType)->getProperties(),
                            $excludedFields
                        );
                    }
                } elseif (class_exists($type)) {
                    $propertyArray[$property->getName()] = $this->getCoursePropertyList(
                        $this->reflectionService->getClassSchema($type)->getProperties(),
                        $excludedFields
                    );
                } else {
                    $propertyArray[$property->getName()] = $property->getName();
                }
            }
        }

        return $propertyArray;
    }

    protected function getCacheIdentifierForStudyCourses(array $options): string
    {
        // create cache Identifier
        if (empty($options)) {
            $optionsIdentifier = 'allStudyCourses';
        } else {
            $optionsIdentifier = json_encode($options, JSON_THROW_ON_ERROR);
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
