<?php

namespace In2code\In2studyfinder\Domain\Model;

/**
 * Department
 */
interface DepartmentInterface
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
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField();
}
