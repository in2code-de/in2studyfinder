<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ForViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('for', 'array', 'The array or \SplObjectStorage to iterated over', true);
        $this->registerArgument('as', 'string', 'The name of the iteration variable', true);

        $this->registerArgument('letter', 'string', '', false, 'letter');
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function render(): string
    {
        $for = $this->arguments['for'];
        $as = $this->arguments['as'];
        $letter = $this->arguments['letter'];

        if (is_array($for)) {
            $sortedStudyCourses = [];
            foreach ($for as $studyCourse) {
                $sortedStudyCourses[substr(strtoupper((string) $studyCourse->getTitle()), 0, 1)][] = $studyCourse;
            }

            $results = [];
            foreach ($sortedStudyCourses as $capitalLetter => $course) {
                $this->setVariable($letter, $capitalLetter);
                $this->setVariable($as, $course);
                $results[] = $this->renderChildren();
            }

            return implode('', $results);
        }

        return '';
    }

    protected function setVariable(string $name, $value): void
    {
        if ($this->templateVariableContainer->exists($name)) {
            $this->templateVariableContainer->remove($name);
        }

        $this->templateVariableContainer->add($name, $value);
    }
}
