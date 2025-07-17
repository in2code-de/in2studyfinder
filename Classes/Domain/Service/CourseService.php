<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Repository\StudyCourseRepository;
use In2code\In2studyfinder\PageTitle\CoursePageTitleProvider;
use In2code\In2studyfinder\Service\PluginService;
use In2code\In2studyfinder\Utility\CacheUtility;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ClassSchema\Property;
use TYPO3\CMS\Extbase\Reflection\Exception\UnknownClassException;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CourseService extends AbstractService
{
    protected array $settings = [];

    /**
     * @var ReflectionService|null
     */
    protected ?ReflectionService $reflectionService = null;

    public function __construct(
        protected StudyCourseRepository $studyCourseRepository,
        protected PluginService $pluginService
    ) {
        $this->reflectionService = GeneralUtility::makeInstance(ReflectionService::class);
    }

    public function findBySearchOptions(array $searchOptions, array $pluginRecord): array
    {
        $storagePids = $this->pluginService->getPluginStoragePids($pluginRecord);

        if ($storagePids !== []) {
            $searchOptions['storagePids'] = $storagePids;
        }

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

    public function getCourseProperties(StudyCourse $course, array $excludedFields = []): array
    {
        return $this->getCoursePropertyList(
            $this->reflectionService->getClassSchema($course)->getProperties(),
            $excludedFields
        );
    }

    /**
     * @param Property[] $objectProperties
     * @throws UnknownClassException
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
                $propertyName = $property->getName();
                $type = $property->getPrimaryType()->getBuiltinType();
                if ($type !== 'object') {
                    $propertyArray[$propertyName] = $propertyName;
                    continue;
                }

                $className = $property->getPrimaryType()->getClassName();
                if ($property->isObjectStorageType()) {
                    $childPropertyClassName = $property->getPrimaryCollectionValueType()->getClassName();
                    if (class_exists($childPropertyClassName)) {
                        $propertyArray[$propertyName] = $this->getCoursePropertyList(
                            $this->reflectionService->getClassSchema($childPropertyClassName)->getProperties(),
                            $excludedFields
                        );
                    }
                } else {
                    $propertyArray[$propertyName] = $this->getCoursePropertyList(
                        $this->reflectionService->getClassSchema($className)->getProperties(),
                        $excludedFields
                    );
                }
            }
        }

        return $propertyArray;
    }
}
