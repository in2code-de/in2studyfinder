<?php

namespace In2code\In2studyfinder\Domain\Model;


/**
 * TypeOfStudy
 */
interface TypeOfStudyInterface
{
    /**
     * Returns the type
     *
     * @return string type
     */
    public function getType();

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType($type);

    /**
     * Returns the option Field
     *
     * @return string type
     */
    public function getOptionField();
}