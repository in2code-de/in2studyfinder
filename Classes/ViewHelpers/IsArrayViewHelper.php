<?php

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class IsArrayViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @param mixed $variable
     *
     * @return boolean
     */
    public function render($variable)
    {
        return is_array($variable);
    }
}
