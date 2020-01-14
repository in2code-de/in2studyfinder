<?php

namespace In2code\In2studyfinder\ViewHelpers\Math;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AdditionViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('value1', 'integer', '', true);
        $this->registerArgument('value2', 'integer', '', true);
    }


    /**
     * @param integer $value1
     * @param integer $value2
     *
     * @return int
     */
    public function render()
    {
        return $this->arguments['value1'] + $this->arguments['value2'];
    }
}
