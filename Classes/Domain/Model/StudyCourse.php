<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use In2code\In2studyfinder\Utility\GlobalDataUtility;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class StudyCourse extends AbstractEntity
{
    public const TABLE = 'tx_in2studyfinder_domain_model_studycourse';

    protected string $title = '';

    protected int $standardPeriodOfStudy = 0;

    protected int $ectsCredits = 0;

    protected float $tuitionFee = 0.0;

    protected string $teaser = '';

    protected string $description = '';

    protected int $universityPlace = 0;

    protected string $metaPagetitle = '';

    protected string $metaKeywords = '';

    protected string $metaDescription = '';

    protected bool $differentPreset = false;

    protected int $sysLanguageUid = 0;

    protected bool $hidden = false;

    protected bool $deleted = false;

    protected int $l10nParent = 0;

    protected ?AcademicDegree $academicDegree = null;

    protected ?Department $department = null;

    protected ?Faculty $faculty = null;

    protected ?GlobalData $globalDataPreset = null;

    /** @var ObjectStorage<Category> */
    protected ObjectStorage $categories;

    /** @var ObjectStorage<TtContent> */
    protected ObjectStorage $contentElements;

    /** @var ObjectStorage<TypeOfStudy> */
    protected ObjectStorage $typesOfStudy;

    /** @var ObjectStorage<CourseLanguage> */
    protected ObjectStorage $courseLanguages;

    /** @var ObjectStorage<AdmissionRequirement> */
    protected ObjectStorage $admissionRequirements;

    /** @var ObjectStorage<StartOfStudy> */
    protected ObjectStorage $startsOfStudy;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->contentElements = new ObjectStorage();
        $this->courseLanguages = new ObjectStorage();
        $this->startsOfStudy = new ObjectStorage();
        $this->typesOfStudy = new ObjectStorage();
        $this->admissionRequirements = new ObjectStorage();
        $this->categories = new ObjectStorage();
    }

    public function getL10nParent(): int
    {
        return $this->l10nParent;
    }

    public function setL10nParent(int $l10nParent): void
    {
        $this->l10nParent = $l10nParent;
    }

    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSysLanguageUid(): int
    {
        return $this->sysLanguageUid;
    }

    public function setSysLanguageUid(int $sysLanguageUid): void
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }

    public function getStandardPeriodOfStudy(): int
    {
        return $this->standardPeriodOfStudy;
    }

    public function setStandardPeriodOfStudy(int $standardPeriodOfStudy): void
    {
        $this->standardPeriodOfStudy = $standardPeriodOfStudy;
    }

    public function getEctsCredits(): int
    {
        return $this->ectsCredits;
    }

    public function setEctsCredits(int $ectsCredits): void
    {
        $this->ectsCredits = $ectsCredits;
    }

    public function getTuitionFee(): float
    {
        return $this->tuitionFee;
    }

    public function setTuitionFee(float $tuitionFee): void
    {
        $this->tuitionFee = $tuitionFee;
    }

    public function getTeaser(): string
    {
        return $this->teaser;
    }

    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getUniversityPlace(): int
    {
        return $this->universityPlace;
    }

    public function setUniversityPlace(int $universityPlace): void
    {
        $this->universityPlace = $universityPlace;
    }

    public function addContentElement(TtContent $contentElement): void
    {
        $this->contentElements->attach($contentElement);
    }

    public function removeContentElement(TtContent $contentElementToRemove): void
    {
        $this->contentElements->detach($contentElementToRemove);
    }

    public function getContentElements(): ObjectStorage
    {
        return $this->contentElements;
    }

    public function setContentElements(ObjectStorage $contentElements): void
    {
        $this->contentElements = $contentElements;
    }

    public function getAcademicDegree(): ?AcademicDegree
    {
        return $this->academicDegree;
    }

    public function setAcademicDegree(AcademicDegree $academicDegree): void
    {
        $this->academicDegree = $academicDegree;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(Faculty $faculty): void
    {
        $this->faculty = $faculty;
    }

    public function getTypesOfStudy(): ObjectStorage
    {
        return $this->typesOfStudy;
    }

    public function setTypesOfStudy(ObjectStorage $typesOfStudy): void
    {
        $this->typesOfStudy = $typesOfStudy;
    }

    public function addTypeOfStudy(TypeOfStudy $typeOfStudy): void
    {
        $this->typesOfStudy->attach($typeOfStudy);
    }

    public function removeTypeOfStudy(TypeOfStudy $typeOfStudyToRemove): void
    {
        $this->typesOfStudy->detach($typeOfStudyToRemove);
    }

    public function getCourseLanguages(): ObjectStorage
    {
        return $this->courseLanguages;
    }

    public function setCourseLanguages(ObjectStorage $courseLanguages): void
    {
        $this->courseLanguages = $courseLanguages;
    }

    public function addCourseLanguage(CourseLanguage $courseLanguage): void
    {
        $this->courseLanguages->attach($courseLanguage);
    }

    public function removeCourseLanguage(CourseLanguage $courseLanguageToRemove): void
    {
        $this->courseLanguages->detach($courseLanguageToRemove);
    }

    public function getAdmissionRequirements(): ObjectStorage
    {
        return $this->admissionRequirements;
    }

    public function setAdmissionRequirements(ObjectStorage $admissionRequirements): void
    {
        $this->admissionRequirements = $admissionRequirements;
    }

    public function addAdmissionRequirement(AdmissionRequirement $admissionRequirement): void
    {
        $this->admissionRequirements->attach($admissionRequirement);
    }

    public function removeAdmissionRequirement(AdmissionRequirement $admissionRequirementToRemove): void
    {
        $this->admissionRequirements->detach($admissionRequirementToRemove);
    }

    public function getStartsOfStudy(): ObjectStorage
    {
        return $this->startsOfStudy;
    }

    public function setStartsOfStudy(ObjectStorage $startsOfStudy): void
    {
        $this->startsOfStudy = $startsOfStudy;
    }

    public function addStartOfStudy(StartOfStudy $startOfStudy): void
    {
        $this->startsOfStudy->attach($startOfStudy);
    }

    public function removeStartOfStudy(StartOfStudy $startOfStudyToRemove): void
    {
        $this->startsOfStudy->detach($startOfStudyToRemove);
    }

    public function getMetaPagetitle(): string
    {
        return $this->metaPagetitle;
    }

    public function setMetaPagetitle(string $metaPagetitle): void
    {
        $this->metaPagetitle = $metaPagetitle;
    }

    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getGlobalDataPreset(): ?GlobalData
    {
        return $this->globalDataPreset;
    }

    public function setGlobalDataPreset(GlobalData $globalDataPreset): void
    {
        $this->globalDataPreset = $globalDataPreset;
    }

    public function isDifferentPreset(): bool
    {
        return $this->differentPreset;
    }

    public function setDifferentPreset(bool $differentPreset): void
    {
        $this->differentPreset = $differentPreset;
    }

    public function getGlobalData(): ?GlobalData
    {
        if ($this->isDifferentPreset()) {
            return $this->getGlobalDataPreset();
        }

        return GlobalDataUtility::getDefaultPreset();
    }

    public function getTitleWithAcademicDegree(): string
    {
        if ($this->getAcademicDegree() instanceof \In2code\In2studyfinder\Domain\Model\AcademicDegree && !in_array($this->getAcademicDegree()->getDegree(), ['', '0'], true)) {
            return $this->getTitle() . ' - ' . $this->getAcademicDegree()->getDegree();
        }

        return $this->getTitle();
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }

    public function removeCategory(Category $category): void
    {
        $this->categories->detach($category);
    }

    public function getDetailPageTitle(): string
    {
        if (!in_array($this->getMetaPagetitle(), ['', '0'], true)) {
            return $this->getMetaPagetitle();
        }

        return $this->getTitleWithAcademicDegree();
    }

    public function getContentElementIdList(): string
    {
        $idList = [];
        if ($this->getContentElements() instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
            foreach ($this->getContentElements() as $contentElement) {
                    $idList[] = $contentElement->getUid();
            }
        }

        return implode(',', $idList);
    }

    public function getInitialLetter(): string {
        return substr(strtoupper($this->getTitle()), 0, 1);
    }
}
