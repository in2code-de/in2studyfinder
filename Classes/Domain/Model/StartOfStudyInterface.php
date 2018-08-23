<?php

namespace In2code\In2studyfinder\Domain\Model;


/**
 * StartOfStudy
 */
interface StartOfStudyInterface
{
    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle();

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title);

    /**
     * Returns the startDate
     *
     * @return string $startDate
     */
    public function getStartDate();

    /**
     * Sets the startDate
     *
     * @param string $startDate
     * @return void
     */
    public function setStartDate($startDate);

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField();
}