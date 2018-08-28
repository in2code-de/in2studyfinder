<?php
/**
 * Created by PhpStorm.
 * User: in2marcus
 * Date: 23.08.18
 * Time: 12:43
 */

namespace In2code\In2studyfinder\Domain\Model;


use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * StudyCourse
 */
interface StudyCourseInterface
{
    /**
     * @param int $uid
     */
    public function setUid($uid);

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
     * Returns the title
     *
     * @return string $sysLanguageUid
     */
    public function getSysLanguageUid();

    /**
     * Sets the title
     *
     * @param string $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid);

    /**
     * Returns the standardPeriodOfStudy
     *
     * @return int $standardPeriodOfStudy
     */
    public function getStandardPeriodOfStudy();

    /**
     * Sets the standardPeriodOfStudy
     *
     * @param int $standardPeriodOfStudy
     * @return void
     */
    public function setStandardPeriodOfStudy($standardPeriodOfStudy);

    /**
     * Returns the ectsCredits
     *
     * @return int $ectsCredits
     */
    public function getEctsCredits();

    /**
     * Sets the ectsCredits
     *
     * @param int $ectsCredits
     * @return void
     */
    public function setEctsCredits($ectsCredits);

    /**
     * Returns the tuitionFee
     *
     * @return float $tuitionFee
     */
    public function getTuitionFee();

    /**
     * Sets the tuitionFee
     *
     * @param float $tuitionFee
     * @return void
     */
    public function setTuitionFee($tuitionFee);

    /**
     * Returns the teaser
     *
     * @return string $teaser
     */
    public function getTeaser();

    /**
     * Sets the teaser
     *
     * @param string $teaser
     * @return void
     */
    public function setTeaser($teaser);

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription();

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description);

    /**
     * Returns the universityPlace
     *
     * @return int $universityPlace
     */
    public function getUniversityPlace();

    /**
     * Sets the universityPlace
     *
     * @param int $universityPlace
     * @return void
     */
    public function setUniversityPlace($universityPlace);

    /**
     * Adds a ContentElement
     *
     * @param TtContentInterface $contentElement
     * @return void
     */
    public function addContentElement($contentElement);

    /**
     * Removes a ContentElement
     *
     * @param TtContentInterface $contentElementToRemove The TtContent to be removed
     * @return void
     */
    public function removeContentElement($contentElementToRemove);

    /**
     * Returns the contentElements
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContent> contentElements
     */
    public function getContentElements();

    /**
     * Sets the contentElements
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContent> $contentElements
     * @return void
     */
    public function setContentElements(ObjectStorage $contentElements);

    /**
     * Returns the academicDegree
     *
     * @return AcademicDegreeInterface $academicDegree
     */
    public function getAcademicDegree();

    /**
     * Sets the academicDegree
     *
     * @param \In2code\In2studyfinder\Domain\Model\AcademicDegreeInterface $academicDegree
     * @return void
     */
    public function setAcademicDegree(AcademicDegreeInterface $academicDegree);

    /**
     * Returns the department
     *
     * @return DepartmentInterface $department
     */
    public function getDepartment();

    /**
     * Sets the department
     *
     * @param DepartmentInterface $department
     * @return void
     */
    public function setDepartment(DepartmentInterface $department);

    /**
     * Returns the faculty
     *
     * @return FacultyInterface $faculty
     */
    public function getFaculty();

    /**
     * Sets the faculty
     *
     * @param FacultyInterface $faculty
     * @return void
     */
    public function setFaculty(FacultyInterface $faculty);

    /**
     * Returns the typesOfStudy
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy> $typesOfStudy
     */
    public function getTypesOfStudy();

    /**
     * Sets the typeOfStudy
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy> $typeOfStudy
     * @return void
     */
    public function setTypesOfStudy(ObjectStorage $typesOfStudy);

    /**
     * Adds a type of study
     *
     * @param TypeOfStudyInterface $typeOfStudy
     * @return void
     */
    public function addTypeOfStudy($typeOfStudy);

    /**
     * Removes a type of study
     *
     * @param TypeOfStudyInterface $typeOfStudyToRemove The type of study to be removed
     * @return void
     */
    public function removeTypeOfStudy($typeOfStudyToRemove);

    /**
     * Returns the courseLanguages
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage> $courseLanguages
     */
    public function getCourseLanguages();

    /**
     * Sets the courseLanguages
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage> $courseLanguages
     * @return void
     */
    public function setCourseLanguages(ObjectStorage $courseLanguages);

    /**
     * Adds a CourseLanguage
     *
     * @param CourseLanguageInterface $courseLanguage
     * @return void
     */
    public function addCourseLanguage($courseLanguage);

    /**
     * Removes a CourseLanguage
     *
     * @param CourseLanguageInterface $courseLanguageToRemove The Course Language to be
     *     removed
     * @return void
     */
    public function removeCourseLanguage($courseLanguageToRemove);

    /**
     * Returns the admissionRequirements
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirement> $admissionRequirements
     */
    public function getAdmissionRequirements();

    /**
     * Sets the admissionRequirements
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirement> $admissionRequirements
     * @return void
     */
    public function setAdmissionRequirements(ObjectStorage $admissionRequirements);

    /**
     * Adds a admissionRequirement
     *
     * @param AdmissionRequirementInterface $admissionRequirement
     * @return void
     */
    public function addAdmissionRequirement($admissionRequirement);

    /**
     * Removes a admissionRequirement
     *
     * @param AdmissionRequirementInterface $admissionRequirementToRemove The type of study
     *     to be removed
     * @return void
     */
    public function removeAdmissionRequirement($admissionRequirementToRemove);

    /**
     * Returns the startsOfStudy
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy> $startsOfStudy
     */
    public function getStartsOfStudy();

    /**
     * Sets the startsOfStudy
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy> $startsOfStudy
     * @return void
     */
    public function setStartsOfStudy(ObjectStorage $startsOfStudy);

    /**
     * Adds a StartOfStudy
     *
     * @param StartOfStudyInterface $startOfStudy
     * @return void
     */
    public function addStartOfStudy($startOfStudy);

    /**
     * Removes a startOfStudy
     *
     * @param StartOfStudyInterface $startOfStudyToRemove The start of study to be removed
     * @return void
     */
    public function removeStartOfStudy($startOfStudyToRemove);

    /**
     * @return string
     */
    public function getMetaPagetitle();

    /**
     * @param string $metaPagetitle
     */
    public function setMetaPagetitle($metaPagetitle);

    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return GlobalDataInterface
     */
    public function getGlobalDataPreset();

    /**
     * @param GlobalDataInterface $globalDataPreset
     */
    public function setGlobalDataPreset($globalDataPreset);

    /**
     * @return bool
     */
    public function isDifferentPreset();

    /**
     * @param bool $differentPreset
     */
    public function setDifferentPreset($differentPreset);

    /**
     * @return GlobalDataInterface|null
     */
    public function getGlobalData();

    public function getTitleWithAcademicDegree();
}