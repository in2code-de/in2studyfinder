<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers\Math;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AdditionViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('value1', 'integer', '', true);
        $this->registerArgument('value2', 'integer', '', true);
    }

    public function render(): int
    {
        return $this->arguments['value1'] + $this->arguments['value2'];
    }
}
