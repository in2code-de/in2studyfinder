<?php

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ForViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('for', 'array', 'The array or \SplObjectStorage to iterated over', true);
        $this->registerArgument('as', 'string', 'The name of the iteration variable', true);

        $this->registerArgument('letter', 'string', '', false, 'letter');
    }

    /**
     * @return string
     */
    public function render()
    {
        $for = $this->arguments['for'];
        $as = $this->arguments['as'];
        $letter = $this->arguments['letter'];

        if (is_array($for)) {
            $sortedStudyCourses = array();
            foreach ($for as $studyCourse) {
                $sortedStudyCourses[substr(strtoupper($studyCourse->getTitle()), 0, 1)][] = $studyCourse;
            }
            $results = array();
            foreach ($sortedStudyCourses as $capitalLetter => $course) {
                $this->setVariable($letter, $capitalLetter);
                $this->setVariable($as, $course);
                $results[] = $this->renderChildren();
            }
            return implode($results);
        }
        return '';
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    protected function setVariable($name, $value)
    {
        if ($this->templateVariableContainer->exists($name)) {
            $this->templateVariableContainer->remove($name);
        }
        $this->templateVariableContainer->add($name, $value);
    }
}
