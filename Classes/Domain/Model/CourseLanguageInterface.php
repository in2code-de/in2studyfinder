<?php

namespace In2code\In2studyfinder\Domain\Model;


/**
 * CourseLanguage
 */
interface CourseLanguageInterface
{
    /**
     * Returns the language
     *
     * @return string language
     */
    public function getLanguage();

    /**
     * Sets the language
     *
     * @param string $language
     * @return void
     */
    public function setLanguage($language);

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField();
}