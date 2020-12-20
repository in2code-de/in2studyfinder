<?php

namespace In2code\In2studyfinder\Domain\Model;

/**
 * AcademicDegree
 */
interface AcademicDegreeInterface
{
    /**
     * Returns the degree
     *
     * @return string degree
     */
    public function getDegree();

    /**
     * Sets the degree
     *
     * @param string $degree
     * @return void
     */
    public function setDegree($degree);

    /**
     * Returns the graduation
     *
     * @return Graduation $graduation
     */
    public function getGraduation();

    /**
     * Sets the graduation
     *
     * @param GraduationInterface $graduation
     * @return void
     */
    public function setGraduation(GraduationInterface $graduation);

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField();
}
