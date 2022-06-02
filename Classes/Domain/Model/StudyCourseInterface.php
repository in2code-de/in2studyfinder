<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
interface StudyCourseInterface
{
    public function setUid(int $uid);

    public function getTitle(): string;

    public function setTitle(string $title);

    public function getSysLanguageUid(): int;

    public function setSysLanguageUid(int $sysLanguageUid);

    public function getStandardPeriodOfStudy(): int;

    public function setStandardPeriodOfStudy(int $standardPeriodOfStudy);

    public function getEctsCredits(): int;

    public function setEctsCredits(int $ectsCredits);

    public function getTuitionFee(): float;

    public function setTuitionFee(float $tuitionFee);

    public function getTeaser(): string;

    public function setTeaser(string $teaser);

    public function getDescription(): string;

    public function setDescription(string $description);

    public function getUniversityPlace(): int;

    public function setUniversityPlace(int $universityPlace);

    public function addContentElement(TtContentInterface $contentElement);

    public function removeContentElement(TtContentInterface $contentElementToRemove);

    public function getContentElements(): ObjectStorage;

    public function setContentElements(ObjectStorage $contentElements);

    public function getAcademicDegree(): ?AcademicDegreeInterface;

    public function setAcademicDegree(AcademicDegreeInterface $academicDegree);

    public function getDepartment(): ?DepartmentInterface;

    public function setDepartment(DepartmentInterface $department);

    public function getFaculty(): ?FacultyInterface;

    public function setFaculty(FacultyInterface $faculty);

    public function getTypesOfStudy(): ObjectStorage;

    public function setTypesOfStudy(ObjectStorage $typesOfStudy);

    public function addTypeOfStudy(TypeOfStudyInterface $typeOfStudy);

    public function removeTypeOfStudy(TypeOfStudyInterface $typeOfStudyToRemove);

    public function getCourseLanguages(): ObjectStorage;

    public function setCourseLanguages(ObjectStorage $courseLanguages);

    public function addCourseLanguage(CourseLanguageInterface $courseLanguage);

    public function removeCourseLanguage(CourseLanguageInterface $courseLanguageToRemove);

    public function getAdmissionRequirements(): ObjectStorage;

    public function setAdmissionRequirements(ObjectStorage $admissionRequirements);

    public function addAdmissionRequirement(AdmissionRequirementInterface $admissionRequirement);

    public function removeAdmissionRequirement(AdmissionRequirementInterface $admissionRequirementToRemove);

    public function getStartsOfStudy(): ObjectStorage;

    public function setStartsOfStudy(ObjectStorage $startsOfStudy);

    public function addStartOfStudy(StartOfStudyInterface $startOfStudy);

    public function removeStartOfStudy(StartOfStudyInterface $startOfStudyToRemove);

    public function getMetaPagetitle(): string;

    public function setMetaPagetitle(string $metaPagetitle);

    public function getMetaKeywords(): string;

    public function setMetaKeywords(string $metaKeywords);

    public function getMetaDescription(): string;

    public function setMetaDescription(string $metaDescription);

    public function getGlobalDataPreset(): ?GlobalDataInterface;

    public function setGlobalDataPreset(GlobalDataInterface $globalDataPreset);

    public function isDifferentPreset(): bool;

    public function setDifferentPreset(bool $differentPreset);

    public function getGlobalData();

    public function getTitleWithAcademicDegree(): string;
}
