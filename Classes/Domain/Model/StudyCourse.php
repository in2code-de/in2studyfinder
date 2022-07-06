<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use In2code\In2studyfinder\Utility\GlobalDataUtility;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StudyCourse extends AbstractEntity implements StudyCourseInterface
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

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>|null
     */
    protected ?ObjectStorage $categories = null;

    /**
     * @var \In2code\In2studyfinder\Domain\Model\AcademicDegree|null
     */
    protected ?AcademicDegreeInterface $academicDegree = null;

    /**
     * @var \In2code\In2studyfinder\Domain\Model\Department|null
     */
    protected ?DepartmentInterface $department = null;

    /**
     * @var \In2code\In2studyfinder\Domain\Model\Faculty|null
     */
    protected ?FacultyInterface $faculty = null;

    /**
     * @var \In2code\In2studyfinder\Domain\Model\GlobalData|null
     */
    protected ?GlobalDataInterface $globalDataPreset = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContent>|null
     */
    protected ?ObjectStorage $contentElements = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudy>|null
     */
    protected ?ObjectStorage $typesOfStudy = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguage>|null
     */
    protected ?ObjectStorage $courseLanguages = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirement>|null
     */
    protected ?ObjectStorage $admissionRequirements = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy>|null
     */
    protected ?ObjectStorage $startsOfStudy = null;

    public function __construct()
    {
        $this->contentElements = new ObjectStorage();

        $this->courseLanguages = new ObjectStorage();
#        $this->addCourseLanguage(new CourseLanguage());

        $this->startsOfStudy = new ObjectStorage();
#        $this->addStartOfStudy(new StartOfStudy());

        $this->typesOfStudy = new ObjectStorage();
#        $this->addTypeOfStudy(new TypeOfStudy());

        $this->admissionRequirements = new ObjectStorage();
#        $this->addAdmissionRequirement(new AdmissionRequirement());

#        $this->academicDegree = new AcademicDegree();
#        $this->department = new Department();
#        $this->faculty = new Faculty();
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

    public function addContentElement(TtContentInterface $contentElement): void
    {
        $this->contentElements->attach($contentElement);
    }

    public function removeContentElement(TtContentInterface $contentElementToRemove): void
    {
        $this->contentElements->detach($contentElementToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContentInterface>|null
     */
    public function getContentElements(): ObjectStorage
    {
        return $this->contentElements;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TtContentInterface>
     */
    public function setContentElements(ObjectStorage $contentElements): void
    {
        $this->contentElements = $contentElements;
    }

    public function getAcademicDegree(): ?AcademicDegreeInterface
    {
        return $this->academicDegree;
    }

    public function setAcademicDegree(AcademicDegreeInterface $academicDegree): void
    {
        $this->academicDegree = $academicDegree;
    }

    public function getDepartment(): ?DepartmentInterface
    {
        return $this->department;
    }

    public function setDepartment(DepartmentInterface $department): void
    {
        $this->department = $department;
    }

    public function getFaculty(): ?FacultyInterface
    {
        return $this->faculty;
    }

    public function setFaculty(FacultyInterface $faculty): void
    {
        $this->faculty = $faculty;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudyInterface>|null
     */
    public function getTypesOfStudy(): ObjectStorage
    {
        return $this->typesOfStudy;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\TypeOfStudyInterface>
     */
    public function setTypesOfStudy(ObjectStorage $typesOfStudy): void
    {
        $this->typesOfStudy = $typesOfStudy;
    }

    public function addTypeOfStudy(TypeOfStudyInterface $typeOfStudy): void
    {
        $this->typesOfStudy->attach($typeOfStudy);
    }

    public function removeTypeOfStudy(TypeOfStudyInterface $typeOfStudyToRemove)
    {
        $this->typesOfStudy->detach($typeOfStudyToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguageInterface>|null
     */
    public function getCourseLanguages(): ObjectStorage
    {
        return $this->courseLanguages;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\CourseLanguageInterface>
     */
    public function setCourseLanguages(ObjectStorage $courseLanguages): void
    {
        $this->courseLanguages = $courseLanguages;
    }

    public function addCourseLanguage(CourseLanguageInterface $courseLanguage): void
    {
        $this->courseLanguages->attach($courseLanguage);
    }

    public function removeCourseLanguage(CourseLanguageInterface $courseLanguageToRemove): void
    {
        $this->courseLanguages->detach($courseLanguageToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirementInterface>|null
     */
    public function getAdmissionRequirements(): ObjectStorage
    {
        return $this->admissionRequirements;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\AdmissionRequirementInterface>
     */
    public function setAdmissionRequirements(ObjectStorage $admissionRequirements): void
    {
        $this->admissionRequirements = $admissionRequirements;
    }

    public function addAdmissionRequirement(AdmissionRequirementInterface $admissionRequirement): void
    {
        $this->admissionRequirements->attach($admissionRequirement);
    }

    public function removeAdmissionRequirement(AdmissionRequirementInterface $admissionRequirementToRemove): void
    {
        $this->admissionRequirements->detach($admissionRequirementToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy>|null
     */
    public function getStartsOfStudy(): ObjectStorage
    {
        return $this->startsOfStudy;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\In2studyfinder\Domain\Model\StartOfStudy>
     */
    public function setStartsOfStudy(ObjectStorage $startsOfStudy): void
    {
        $this->startsOfStudy = $startsOfStudy;
    }

    /**
     * @param StartOfStudy
     */
    public function addStartOfStudy(StartOfStudyInterface $startOfStudy): void
    {
        $this->startsOfStudy->attach($startOfStudy);
    }

    /**
     * @param StartOfStudy
     */
    public function removeStartOfStudy(StartOfStudyInterface $startOfStudyToRemove): void
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

    public function getGlobalDataPreset(): GlobalDataInterface
    {
        return $this->globalDataPreset;
    }

    public function setGlobalDataPreset(GlobalDataInterface $globalDataPreset): void
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

    public function getGlobalData(): ?GlobalDataInterface
    {
        if ($this->isDifferentPreset()) {
            $globalData = $this->getGlobalDataPreset();
        } else {
            $globalData = GlobalDataUtility::getDefaultPreset();
        }

        return $globalData;
    }

    public function getTitleWithAcademicDegree(): string
    {
        if (!empty($this->getAcademicDegree()) && !empty($this->getAcademicDegree()->getDegree())) {
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

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>|null
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
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
        if (!empty($this->getMetaPagetitle())) {
            $detailPageTitle = $this->getMetaPagetitle();
        } else {
            $detailPageTitle = $this->getTitleWithAcademicDegree();
        }

        return $detailPageTitle;
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
     */
    public static function cmpObj(StudyCourseInterface $studyCourseA, StudyCourseInterface $studyCourseB): int
    {
        return strcmp(strtolower($studyCourseA->getTitle()), strtolower($studyCourseB->getTitle()));
    }
}
