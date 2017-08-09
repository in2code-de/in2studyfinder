<?php

namespace In2code\In2studyfinder\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
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
class StudyCourseListContext extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * teaser
     *
     * @var string
     */
    protected $teaser = '';

    /**
     * academicDegree
     *
     * @var \In2code\In2studyfinder\Domain\Model\AcademicDegree
     */
    protected $academicDegree;

    /**
     * typeOfStudy
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy>
     */
    protected $typesOfStudy = null;

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
     * courseLanguages
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage>
     */
    protected $courseLanguages = null;

    /**
     * department
     *
     * @var \In2code\In2studyfinder\Domain\Model\Department
     */
    protected $department = null;

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
        $this->courseLanguages = new ObjectStorage();
        $this->startsOfStudy = new ObjectStorage();
        $this->typesOfStudy = new ObjectStorage();
        $this->admissionRequirements = new ObjectStorage();
    }

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
     * @param \In2code\In2studyfinder\Domain\Model\AdmissionRequirement $admissionRequirementToRemove The type of study
     *     to be removed
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
     * @param \In2code\In2studyfinder\Domain\Model\CourseLanguage $courseLanguageToRemove The Course Language to be
     *     removed
     * @return void
     */
    public function removeCourseLanguage($courseLanguageToRemove)
    {
        $this->courseLanguages->detach($courseLanguageToRemove);
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

    public function getTitleWithAcademicDegree()
    {
        return $this->getTitle() . ' - ' . $this->getAcademicDegree()->getDegree();
    }

    /**
     * compare function for sorting
     *
     * DE: ein Workaround für die Sortierung nach Titel. Da die Sortierung über das
     * Repository ($defaultOrderings) bei übersetzten Datensätzen nicht richtig funktioniert.
     * Soll nach anderen Kriterien sortiert werden kann diese Funktion einfach überschrieben werden.
     *
     * z.B.
     *
     * public static function cmpObj($studyCourseA, $studyCourseB)
     * {
     *     $al = strtolower($studyCourseA->getTitle());
     *     $bl = strtolower($studyCourseB->getTitle());
     *
     *     if ($al == $bl) {
     *       return $studyCourseB->getAcademicDegree()->getSorting() - $studyCourseB->getAcademicDegree()->getSorting();
     *     }
     *
     *     return strcmp($studyCourseA->getTitle(), $studyCourseB->getTitle());
     * }
     *
     * würde erst nach Titel alphabetisch und danach nach dem Akademischem Grad ("sorting" im Backend durch den
     * Redakteur gepflegt) sortieren.
     *
     * @param $studyCourseA StudyCourse
     * @param $studyCourseB StudyCourse
     * @return int
     */
    public static function cmpObj($studyCourseA, $studyCourseB)
    {
        return strcmp(strtolower($studyCourseA->getTitle()), strtolower($studyCourseB->getTitle()));
    }
}
