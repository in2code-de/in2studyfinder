<?php

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IsArrayViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('variable', 'mixed', 'variable to check', true);
    }

    /**
     * @return boolean
     */
    public function render()
    {
        return is_array($this->arguments['variable']);
    }
}
