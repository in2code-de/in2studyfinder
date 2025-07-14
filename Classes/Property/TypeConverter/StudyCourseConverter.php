<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Property\TypeConverter;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

class StudyCourseConverter extends PersistentObjectConverter
{
    /**
     * @var string
     */
    public $targetType;

    /**
     * @var string[]
     */
    protected $sourceTypes = [
        StudyCourse::class
    ];

    /**
     * @var int
     */
    protected $priority = 30;

    public function __construct(string $targetType = 'object')
    {
        $this->targetType = $targetType;
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
    }

    /**
     * Convert an object from $source to an entity or a value object.
     *
     * @return object|null the target type
     * @throws \InvalidArgumentException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        PropertyMappingConfigurationInterface $configuration = null
    ): ?object {
        return parent::convertFrom($source, $this->targetType, $convertedChildProperties, $configuration);
    }
}
