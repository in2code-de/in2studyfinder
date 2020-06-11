<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $filterName = '';

    /**
     * @var string
     */
    protected $propertyPath = '';

    /**
     * @var string
     */
    protected $frontendLabel = '';

    /**
     * @var bool
     */
    protected $disabledInFrontend = false;

    /**
     * @var array
     */
    protected $filterOptions = [];

    /**
     * @var Logger
     */
    protected $logger = null;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(static::class);
    }

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return $this->filterName;
    }

    /**
     * @param string $filterName
     * @return AbstractFilter
     */
    public function setFilterName(string $filterName): AbstractFilter
    {
        $this->filterName = $filterName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /**
     * @param string $propertyPath
     */
    public function setPropertyPath(string $propertyPath)
    {
        $this->propertyPath = $propertyPath;
    }

    /**
     * @return string
     */
    public function getFrontendLabel(): string
    {
        return $this->frontendLabel;
    }

    /**
     * @param string $frontendLabel
     */
    public function setFrontendLabel(string $frontendLabel)
    {
        $this->frontendLabel = $frontendLabel;
    }

    /**
     * @return bool
     */
    public function isDisabledInFrontend(): bool
    {
        return $this->disabledInFrontend;
    }

    /**
     * @param bool $disabledInFrontend
     */
    public function setDisabledInFrontend(bool $disabledInFrontend)
    {
        $this->disabledInFrontend = $disabledInFrontend;
    }

    /**
     * @return array
     */
    public function getFilterOptions(): array
    {
        return $this->filterOptions;
    }

    /**
     * @param array $filterOptions
     */
    public function setFilterOptions(array $filterOptions)
    {
        $this->filterOptions = $filterOptions;
    }

    /**
     * Reconstitutes a property.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return bool
     */
    public function _setProperty(string $propertyName, $propertyValue)
    {
        if ($this->_hasProperty($propertyName)) {
            $this->{$propertyName} = $propertyValue;
            return true;
        }
        return false;
    }

    /**
     * Returns the property value of the given property name.
     *
     * @param string $propertyName
     * @return bool TRUE bool true if the property exists, FALSE if it doesn't exist or NULL in case of an error.
     */
    public function _hasProperty($propertyName)
    {
        return property_exists($this, $propertyName);
    }

    /**
     * @param array $filterConfiguration
     */
    public function setPropertiesByConfiguration(array $filterConfiguration)
    {
        foreach ($filterConfiguration as $property => $value) {
            if ($this->_hasProperty($property)) {
                $this->_setProperty($property, $value);
            }
        }
    }

    /**
     * @param array $filterConfiguration
     * @param string $filterName
     * @return bool
     */
    public function isFilterConfigurationValid(array &$filterConfiguration, string $filterName): bool
    {
        $isValid = true;

        if (!array_key_exists('propertyPath', $filterConfiguration)) {
            $isValid = false;
            // @todo log missing propertyPath in filter configuration
        }

        if (!array_key_exists('frontendLabel', $filterConfiguration)) {
            $isValid = false;
            // @todo log missing frontendLabel in filter configuration
        }

        if (!$isValid) {
            $this->logger->warning(
                'The given filter configuration for the filter: ' . $filterName . ' is not valid. This filter will be ignored!',
                [
                    'filterName' => $filterName,
                    'filterProperties' => $filterConfiguration,
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]
                ]
            );
        }

        return $isValid;
    }
}
