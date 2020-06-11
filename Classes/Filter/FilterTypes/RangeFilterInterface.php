<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter\FilterTypes;

interface RangeFilterInterface
{
    public function setMin(int $min);
    public function setMax(int $max);

    public function getMin(): int;
    public function getMax(): int;
}
