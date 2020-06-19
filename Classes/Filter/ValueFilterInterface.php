<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter;

interface ValueFilterInterface extends FilterInterface
{
    /**
     * @return array
     */
    public function getFilterOptions(): array;

    /**
     * @param array $filterOptions
     */
    public function setFilterOptions(array $filterOptions);

    public function buildFilterOptions();
}
