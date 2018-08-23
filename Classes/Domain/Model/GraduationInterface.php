<?php

namespace In2code\In2studyfinder\Domain\Model;

/**
 * CourseLanguage
 */
interface GraduationInterface
{
    /**
     * Returns the graduation
     *
     * @return string graduation
     */
    public function getTitle();

    /**
     * Sets the language
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title);

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField();
}