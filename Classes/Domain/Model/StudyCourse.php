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

/**
 * StudyCourse
 */
class StudyCourse extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';
    
    /**
     * standardPeriodOfStudy
     *
     * @var int
     */
    protected $standardPeriodOfStudy = 0;
    
    /**
     * ectsCredits
     *
     * @var int
     */
    protected $ectsCredits = 0;
    
    /**
     * tuitionFee
     *
     * @var float
     */
    protected $tuitionFee = 0.0;
    
    /**
     * teaser
     *
     * @var string
     */
    protected $teaser = '';
    
    /**
     * description
     *
     * @var string
     */
    protected $description = '';
    
    /**
     * universityPlace
     *
     * @var int
     */
    protected $universityPlace = 0;
    
    /**
     * contentElements
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContent>
     * @cascade remove
     */
    protected $contentElements = null;
    
    /**
     * academicDegree
     *
     * @var \In2code\In2studyfinder\Domain\Model\AcademicDegree
     */
    protected $academicDegree = null;
    
    /**
     * department
     *
     * @var \In2code\In2studyfinder\Domain\Model\Department
     */
    protected $department = null;
    
    /**
     * faculty
     *
     * @var \In2code\In2studyfinder\Domain\Model\Faculty
     */
    protected $faculty = null;
    
    /**
     * typeOfStudy
     *
     * @var \In2code\In2studyfinder\Domain\Model\TypeOfStudy
     */
    protected $typeOfStudy = null;
    
    /**
     * courseLanguage
     *
     * @var \In2code\In2studyfinder\Domain\Model\CourseLanguage
     */
    protected $courseLanguage = null;
    
    /**
     * admissionRequirements
     *
     * @var \In2code\In2studyfinder\Domain\Model\AdmissionRequirements
     */
    protected $admissionRequirements = null;
    
    /**
     * startOfStudy
     *
     * @var \In2code\In2studyfinder\Domain\Model\StartOfStudy
     */
    protected $startOfStudy = null;
    
    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Returns the standardPeriodOfStudy
     *
     * @return int $standardPeriodOfStudy
     */
    public function getStandardPeriodOfStudy()
    {
        return $this->standardPeriodOfStudy;
    }
    
    /**
     * Sets the standardPeriodOfStudy
     *
     * @param int $standardPeriodOfStudy
     * @return void
     */
    public function setStandardPeriodOfStudy($standardPeriodOfStudy)
    {
        $this->standardPeriodOfStudy = $standardPeriodOfStudy;
    }
    
    /**
     * Returns the ectsCredits
     *
     * @return int $ectsCredits
     */
    public function getEctsCredits()
    {
        return $this->ectsCredits;
    }
    
    /**
     * Sets the ectsCredits
     *
     * @param int $ectsCredits
     * @return void
     */
    public function setEctsCredits($ectsCredits)
    {
        $this->ectsCredits = $ectsCredits;
    }
    
    /**
     * Returns the tuitionFee
     *
     * @return float $tuitionFee
     */
    public function getTuitionFee()
    {
        return $this->tuitionFee;
    }
    
    /**
     * Sets the tuitionFee
     *
     * @param float $tuitionFee
     * @return void
     */
    public function setTuitionFee($tuitionFee)
    {
        $this->tuitionFee = $tuitionFee;
    }
    
    /**
     * Returns the teaser
     *
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }
    
    /**
     * Sets the teaser
     *
     * @param string $teaser
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }
    
    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * Returns the universityPlace
     *
     * @return int $universityPlace
     */
    public function getUniversityPlace()
    {
        return $this->universityPlace;
    }
    
    /**
     * Sets the universityPlace
     *
     * @param int $universityPlace
     * @return void
     */
    public function setUniversityPlace($universityPlace)
    {
        $this->universityPlace = $universityPlace;
    }
    
    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }
    
    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->contentElements = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }
    
    /**
     * Adds a ContentElement
     *
     * @param \In2code\In2studyfinder\Domain\Model\TtContent $contentElement
     * @return void
     */
    public function addContentElement($contentElement)
    {
        $this->contentElements->attach($contentElement);
    }
    
    /**
     * Removes a ContentElement
     *
     * @param \In2code\In2studyfinder\Domain\Model\TtContent $contentElementToRemove The TtContent to be removed
     * @return void
     */
    public function removeContentElement($contentElementToRemove)
    {
        $this->contentElements->detach($contentElementToRemove);
    }
    
    /**
     * Returns the contentElements
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContent> contentElements
     */
    public function getContentElements()
    {
        return $this->contentElements;
    }
    
    /**
     * Sets the contentElements
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContent> $contentElements
     * @return void
     */
    public function setContentElements(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $contentElements)
    {
        $this->contentElements = $contentElements;
    }
    
    /**
     * Returns the academicDegree
     *
     * @return \In2code\In2studyfinder\Domain\Model\AcademicDegree $academicDegree
     */
    public function getAcademicDegree()
    {
        return $this->academicDegree;
    }
    
    /**
     * Sets the academicDegree
     *
     * @param \In2code\In2studyfinder\Domain\Model\AcademicDegree $academicDegree
     * @return void
     */
    public function setAcademicDegree(\In2code\In2studyfinder\Domain\Model\AcademicDegree $academicDegree)
    {
        $this->academicDegree = $academicDegree;
    }
    
    /**
     * Returns the department
     *
     * @return \In2code\In2studyfinder\Domain\Model\Department $department
     */
    public function getDepartment()
    {
        return $this->department;
    }
    
    /**
     * Sets the department
     *
     * @param \In2code\In2studyfinder\Domain\Model\Department $department
     * @return void
     */
    public function setDepartment(\In2code\In2studyfinder\Domain\Model\Department $department)
    {
        $this->department = $department;
    }
    
    /**
     * Returns the faculty
     *
     * @return \In2code\In2studyfinder\Domain\Model\Faculty $faculty
     */
    public function getFaculty()
    {
        return $this->faculty;
    }
    
    /**
     * Sets the faculty
     *
     * @param \In2code\In2studyfinder\Domain\Model\Faculty $faculty
     * @return void
     */
    public function setFaculty(\In2code\In2studyfinder\Domain\Model\Faculty $faculty)
    {
        $this->faculty = $faculty;
    }
    
    /**
     * Returns the typeOfStudy
     *
     * @return \In2code\In2studyfinder\Domain\Model\TypeOfStudy $typeOfStudy
     */
    public function getTypeOfStudy()
    {
        return $this->typeOfStudy;
    }
    
    /**
     * Sets the typeOfStudy
     *
     * @param \In2code\In2studyfinder\Domain\Model\TypeOfStudy $typeOfStudy
     * @return void
     */
    public function setTypeOfStudy(\In2code\In2studyfinder\Domain\Model\TypeOfStudy $typeOfStudy)
    {
        $this->typeOfStudy = $typeOfStudy;
    }
    
    /**
     * Returns the courseLanguage
     *
     * @return \In2code\In2studyfinder\Domain\Model\CourseLanguage $courseLanguage
     */
    public function getCourseLanguage()
    {
        return $this->courseLanguage;
    }
    
    /**
     * Sets the courseLanguage
     *
     * @param \In2code\In2studyfinder\Domain\Model\CourseLanguage $courseLanguage
     * @return void
     */
    public function setCourseLanguage(\In2code\In2studyfinder\Domain\Model\CourseLanguage $courseLanguage)
    {
        $this->courseLanguage = $courseLanguage;
    }
    
    /**
     * Returns the admissionRequirements
     *
     * @return \In2code\In2studyfinder\Domain\Model\AdmissionRequirements $admissionRequirements
     */
    public function getAdmissionRequirements()
    {
        return $this->admissionRequirements;
    }
    
    /**
     * Sets the admissionRequirements
     *
     * @param \In2code\In2studyfinder\Domain\Model\AdmissionRequirements $admissionRequirements
     * @return void
     */
    public function setAdmissionRequirements(\In2code\In2studyfinder\Domain\Model\AdmissionRequirements $admissionRequirements)
    {
        $this->admissionRequirements = $admissionRequirements;
    }
    
    /**
     * Returns the startOfStudy
     *
     * @return \In2code\In2studyfinder\Domain\Model\StartOfStudy $startOfStudy
     */
    public function getStartOfStudy()
    {
        return $this->startOfStudy;
    }
    
    /**
     * Sets the startOfStudy
     *
     * @param \In2code\In2studyfinder\Domain\Model\StartOfStudy $startOfStudy
     * @return void
     */
    public function setStartOfStudy(\In2code\In2studyfinder\Domain\Model\StartOfStudy $startOfStudy)
    {
        $this->startOfStudy = $startOfStudy;
    }

}
