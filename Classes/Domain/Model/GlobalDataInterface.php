<?php

namespace In2code\In2studyfinder\Domain\Model;

/**
 * StudyCourse
 */
interface GlobalDataInterface
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
     * @return boolean
     */
    public function isDefaultPreset();

    /**
     * @param boolean $defaultPreset
     */
    public function setDefaultPreset($defaultPreset);
}
