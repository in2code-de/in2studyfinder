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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * StudyCourse
 */
class StudyCourse extends AbstractEntity
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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy>
     */
    protected $typesOfStudy = null;

    /**
     * courseLanguages
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage>
     */
    protected $courseLanguages = null;

    /**
     * admissionRequirements
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirement>
     */
    protected $admissionRequirements = null;

    /**
     * startOfStudy
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy>
     */
    protected $startsOfStudy = null;

    /**
     * Meta Pagetitle
     *
     * @var string
     */
    protected $metaPagetitle = '';

    /**
     * Meta Keywords
     *
     * @var string
     */
    protected $metaKeywords = '';

    /**
     * Meta Description
     *
     * @var string
     */
    protected $metaDescription = '';

    /**
     * sysLanguageUid
     *
     * @var integer
     */
    protected $sysLanguageUid = 0;

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
     * Returns the title
     *
     * @return string $sysLanguageUid
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * Sets the title
     *
     * @param string $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
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
        $this->contentElements = new ObjectStorage();

        $this->courseLanguages = new ObjectStorage();
        $this->addCourseLanguage(new CourseLanguage());

        $this->startsOfStudy = new ObjectStorage();
        $this->addStartOfStudy(new StartOfStudy());

        $this->typesOfStudy = new ObjectStorage();
        $this->addTypeOfStudy(new TypeOfStudy());

        $this->admissionRequirements = new ObjectStorage();
        $this->addAdmissionRequirement(new AdmissionRequirement());

        $this->academicDegree = new AcademicDegree();
        $this->department = new Department();
        $this->faculty = new Faculty();
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
    public function setContentElements(ObjectStorage $contentElements)
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
    public function setAcademicDegree(AcademicDegree $academicDegree)
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
    public function setDepartment(Department $department)
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
    public function setFaculty(Faculty $faculty)
    {
        $this->faculty = $faculty;
    }

    /**
     * Returns the typesOfStudy
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy> $typesOfStudy
     */
    public function getTypesOfStudy()
    {
        return $this->typesOfStudy;
    }

    /**
     * Sets the typeOfStudy
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy> $typeOfStudy
     * @return void
     */
    public function setTypesOfStudy(ObjectStorage $typesOfStudy)
    {
        $this->typesOfStudy = $typesOfStudy;
    }

    /**
     * Adds a type of study
     *
     * @param \In2code\In2studyfinder\Domain\Model\TypeOfStudy $typeOfStudy
     * @return void
     */
    public function addTypeOfStudy($typeOfStudy)
    {
        $this->typesOfStudy->attach($typeOfStudy);
    }

    /**
     * Removes a type of study
     *
     * @param \In2code\In2studyfinder\Domain\Model\TypeOfStudy $typeOfStudyToRemove The type of study to be removed
     * @return void
     */
    public function removeTypeOfStudy($typeOfStudyToRemove)
    {
        $this->typesOfStudy->detach($typeOfStudyToRemove);
    }

    /**
     * Returns the courseLanguages
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage> $courseLanguages
     */
    public function getCourseLanguages()
    {
        return $this->courseLanguages;
    }

    /**
     * Sets the courseLanguages
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage> $courseLanguages
     * @return void
     */
    public function setCourseLanguages(ObjectStorage $courseLanguages)
    {
        $this->courseLanguages = $courseLanguages;
    }

    /**
     * Adds a CourseLanguage
     *
     * @param \In2code\In2studyfinder\Domain\Model\CourseLanguage $courseLanguage
     * @return void
     */
    public function addCourseLanguage($courseLanguage)
    {
        $this->courseLanguages->attach($courseLanguage);
    }

    /**
     * Removes a CourseLanguage
     *
     * @param \In2code\In2studyfinder\Domain\Model\CourseLanguage $courseLanguageToRemove The Course Language to be removed
     * @return void
     */
    public function removeCourseLanguage($courseLanguageToRemove)
    {
        $this->contentElements->detach($courseLanguageToRemove);
    }

    /**
     * Returns the admissionRequirements
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirement> $admissionRequirements
     */
    public function getAdmissionRequirements()
    {
        return $this->admissionRequirements;
    }

    /**
     * Sets the admissionRequirements
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirement> $admissionRequirements
     * @return void
     */
    public function setAdmissionRequirements(ObjectStorage $admissionRequirements)
    {
        $this->admissionRequirements = $admissionRequirements;
    }

    /**
     * Adds a admissionRequirement
     *
     * @param \In2code\In2studyfinder\Domain\Model\AdmissionRequirement $admissionRequirement
     * @return void
     */
    public function addAdmissionRequirement($admissionRequirement)
    {
        $this->admissionRequirements->attach($admissionRequirement);
    }

    /**
     * Removes a admissionRequirement
     *
     * @param \In2code\In2studyfinder\Domain\Model\AdmissionRequirement $admissionRequirementToRemove The type of study to be removed
     * @return void
     */
    public function removeAdmissionRequirement($admissionRequirementToRemove)
    {
        $this->admissionRequirements->detach($admissionRequirementToRemove);
    }

    /**
     * Returns the startsOfStudy
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy> $startsOfStudy
     */
    public function getStartsOfStudy()
    {
        return $this->startsOfStudy;
    }

    /**
     * Sets the startsOfStudy
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy> $startsOfStudy
     * @return void
     */
    public function setStartsOfStudy(ObjectStorage $startsOfStudy)
    {
        $this->startsOfStudy = $startsOfStudy;
    }

    /**
     * Adds a StartOfStudy
     *
     * @param \In2code\In2studyfinder\Domain\Model\StartOfStudy $startOfStudy
     * @return void
     */
    public function addStartOfStudy($startOfStudy)
    {
        $this->startsOfStudy->attach($startOfStudy);
    }

    /**
     * Removes a startOfStudy
     *
     * @param \In2code\In2studyfinder\Domain\Model\StartOfStudy $startOfStudyToRemove The start of study to be removed
     * @return void
     */
    public function removeStartOfStudy($startOfStudyToRemove)
    {
        $this->startsOfStudy->detach($startOfStudyToRemove);
    }

    /**
     * @return string
     */
    public function getMetaPagetitle()
    {
        return $this->metaPagetitle;
    }

    /**
     * @param string $metaPagetitle
     */
    public function setMetaPagetitle($metaPagetitle)
    {
        $this->metaPagetitle = $metaPagetitle;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

}
