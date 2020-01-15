<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * AcademicDegree
 */
class AcademicDegree extends AbstractEntity implements AcademicDegreeInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_academicdegree';

    /**
     * degree
     *
     * @var string
     */
    protected $degree = '';

    /**
     * graduation
     *
     * @var \In2code\In2studyfinder\Domain\Model\Graduation
     */
    protected $graduation;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->graduation = new Graduation();
    }

    /**
     * Returns the degree
     *
     * @return string degree
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * Sets the degree
     *
     * @param string $degree
     * @return void
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;
    }

    /**
     * Returns the graduation
     *
     * @return Graduation $graduation
     */
    public function getGraduation()
    {
        return $this->graduation;
    }

    /**
     * Sets the graduation
     *
     * @param GraduationInterface $graduation
     * @return void
     */
    public function setGraduation(GraduationInterface $graduation)
    {
        $this->graduation = $graduation;
    }

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField()
    {
        return $this->getDegree();
    }
}
