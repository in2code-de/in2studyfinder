<?php

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class ForViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @param array $for
     * @param string $as
     * @param string $letter
     * @return string
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function render($for, $as, $letter = 'letter')
    {
        if (is_array($for)) {
            $sortedStudyCourses = [];
            foreach ($for as $studyCourse) {
                $sortedStudyCourses[substr(strtoupper($studyCourse->getTitle()), 0, 1)][] = $studyCourse;
            }
            $results = [];
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
