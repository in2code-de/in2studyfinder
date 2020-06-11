<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter\FilterTypes;

use In2code\In2studyfinder\Filter\AbstractFilter;

class RangeFilter extends AbstractFilter implements RangeFilterInterface
{
    /**
     * @var int
     */
    protected $min = 0;

    /**
     * @var int
     */
    protected $max = 999;

    public function setMin(int $min)
    {
        $this->min = $min;
    }

    public function setMax(int $max)
    {
        $this->max = $max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }
}
