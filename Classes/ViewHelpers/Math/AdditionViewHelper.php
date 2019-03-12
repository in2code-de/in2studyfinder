<?php

namespace In2code\In2studyfinder\ViewHelpers\Math;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class AdditionViewHelper extends AbstractViewHelper
{
    /**
     * @param integer $value1
     * @param integer $value2
     *
     * @return int
     */
    public function render($value1, $value2)
    {
        return $value1 + $value2;
    }
}
