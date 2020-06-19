<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter;

use TYPO3\CMS\Core\Routing\Aspect\AspectTrait;

interface RangeFilterInterface extends FilterInterface
{
    public function setMin(int $min);
    public function setMax(int $max);

    public function getMin(): int;
    public function getMax(): int;
}
