<?php
namespace In2code\In2studyfinder\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * AcademicDegree
 */
class AcademicDegree extends AbstractEntity implements AcademicDegreeInterface
{
    /**
     * degree
     *
     * @var string
     * @validate NotEmpty
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
