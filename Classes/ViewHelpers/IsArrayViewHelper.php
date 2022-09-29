<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IsArrayViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('variable', 'mixed', 'variable to check', true);
    }

    public function render(): bool
    {
        return is_array($this->arguments['variable']);
    }
}
