<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter\FilterTypes;

use In2code\In2studyfinder\Filter\AbstractFilter;

class NotEmptyFilter extends AbstractFilter implements ValueFilterInterface
{
    public function buildFilterOptions()
    {
        $this->setFilterOptions([true, false]);
    }
}
