<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter;

interface FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string;

    /**
     * @param string $filterName
     */
    public function setFilterName(string $filterName);

    /**
     * @return string
     */
    public function getPropertyPath(): string;

    /**
     * @param string $propertyPath
     */
    public function setPropertyPath(string $propertyPath);

    /**
     * @return string
     */
    public function getFrontendLabel(): string;

    /**
     * @param string $frontendLabel
     */
    public function setFrontendLabel(string $frontendLabel);

    /**
     * @return bool
     */
    public function isDisabledInFrontend(): bool;

    /**
     * @param bool $disabledInFrontend
     */
    public function setDisabledInFrontend(bool $disabledInFrontend);

    /**
     * @param array $filterConfiguration
     * @return mixed
     */
    public function setPropertiesByConfiguration(array $filterConfiguration);

    /**
     * @param array $filterConfiguration
     * @param string $filterName
     * @return bool
     */
    public function isFilterConfigurationValid(array &$filterConfiguration, string $filterName): bool;
}
