<?php
namespace In2code\In2studyfinder\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \In2code\In2studyfinder\Domain\Model\StudyCourse.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Sebastian Stein <sebastian.stein@in2code.de>
 */
class StudyCourseTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \In2code\In2studyfinder\Domain\Model\StudyCourse
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = new \In2code\In2studyfinder\Domain\Model\StudyCourse();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStandardPeriodOfStudyReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setStandardPeriodOfStudyForIntSetsStandardPeriodOfStudy()
    {
    }

    /**
     * @test
     */
    public function getEctsCreditsReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setEctsCreditsForIntSetsEctsCredits()
    {
    }

    /**
     * @test
     */
    public function getTuitionFeeReturnsInitialValueForFloat()
    {
        $this->assertSame(
            0.0,
            $this->subject->getTuitionFee()
        );
    }

    /**
     * @test
     */
    public function setTuitionFeeForFloatSetsTuitionFee()
    {
        $this->subject->setTuitionFee(3.14159265);

        $this->assertAttributeEquals(
            3.14159265,
            'tuitionFee',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getTeaserReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser()
    {
        $this->subject->setTeaser('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'teaser',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getUniversityPlaceReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setUniversityPlaceForIntSetsUniversityPlace()
    {
    }

    /**
     * @test
     */
    public function getContentElementsReturnsInitialValueForTtContent()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getContentElements()
        );
    }

    /**
     * @test
     */
    public function setContentElementsForObjectStorageContainingTtContentSetsContentElements()
    {
        $contentElement = new \In2code\In2studyfinder\Domain\Model\TtContent();
        $objectStorageHoldingExactlyOneContentElements = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneContentElements->attach($contentElement);
        $this->subject->setContentElements($objectStorageHoldingExactlyOneContentElements);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneContentElements,
            'contentElements',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addContentElementToObjectStorageHoldingContentElements()
    {
        $contentElement = new \In2code\In2studyfinder\Domain\Model\TtContent();
        $contentElementsObjectStorageMock = $this->getMock(
            'TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('attach'),
            array(),
            '',
            false
        );
        $contentElementsObjectStorageMock->expects($this->once())->method('attach')->with(
            $this->equalTo($contentElement)
        );
        $this->inject($this->subject, 'contentElements', $contentElementsObjectStorageMock);

        $this->subject->addContentElement($contentElement);
    }

    /**
     * @test
     */
    public function removeContentElementFromObjectStorageHoldingContentElements()
    {
        $contentElement = new \In2code\In2studyfinder\Domain\Model\TtContent();
        $contentElementsObjectStorageMock = $this->getMock(
            'TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            array('detach'),
            array(),
            '',
            false
        );
        $contentElementsObjectStorageMock->expects($this->once())->method('detach')->with(
            $this->equalTo($contentElement)
        );
        $this->inject($this->subject, 'contentElements', $contentElementsObjectStorageMock);

        $this->subject->removeContentElement($contentElement);
    }

    /**
     * @test
     */
    public function getAcademicDegreeReturnsInitialValueForAcademicDegree()
    {
        $this->assertEquals(
            null,
            $this->subject->getAcademicDegree()
        );
    }

    /**
     * @test
     */
    public function setAcademicDegreeForAcademicDegreeSetsAcademicDegree()
    {
        $academicDegreeFixture = new \In2code\In2studyfinder\Domain\Model\AcademicDegree();
        $this->subject->setAcademicDegree($academicDegreeFixture);

        $this->assertAttributeEquals(
            $academicDegreeFixture,
            'academicDegree',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDepartmentReturnsInitialValueForDepartment()
    {
        $this->assertEquals(
            null,
            $this->subject->getDepartment()
        );
    }

    /**
     * @test
     */
    public function setDepartmentForDepartmentSetsDepartment()
    {
        $departmentFixture = new \In2code\In2studyfinder\Domain\Model\Department();
        $this->subject->setDepartment($departmentFixture);

        $this->assertAttributeEquals(
            $departmentFixture,
            'department',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getFacultyReturnsInitialValueForFaculty()
    {
        $this->assertEquals(
            null,
            $this->subject->getFaculty()
        );
    }

    /**
     * @test
     */
    public function setFacultyForFacultySetsFaculty()
    {
        $facultyFixture = new \In2code\In2studyfinder\Domain\Model\Faculty();
        $this->subject->setFaculty($facultyFixture);

        $this->assertAttributeEquals(
            $facultyFixture,
            'faculty',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTypeOfStudyReturnsInitialValueForTypeOfStudy()
    {
        $this->assertEquals(
            null,
            $this->subject->getTypeOfStudy()
        );
    }

    /**
     * @test
     */
    public function setTypeOfStudyForTypeOfStudySetsTypeOfStudy()
    {
        $typeOfStudyFixture = new \In2code\In2studyfinder\Domain\Model\TypeOfStudy();
        $this->subject->setTypeOfStudy($typeOfStudyFixture);

        $this->assertAttributeEquals(
            $typeOfStudyFixture,
            'typeOfStudy',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCourseLanguageReturnsInitialValueForCourseLanguage()
    {
        $this->assertEquals(
            null,
            $this->subject->getCourseLanguage()
        );
    }

    /**
     * @test
     */
    public function setCourseLanguageForCourseLanguageSetsCourseLanguage()
    {
        $courseLanguageFixture = new \In2code\In2studyfinder\Domain\Model\CourseLanguage();
        $this->subject->setCourseLanguage($courseLanguageFixture);

        $this->assertAttributeEquals(
            $courseLanguageFixture,
            'courseLanguage',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAdmissionRequirementsReturnsInitialValueForAdmissionRequirements()
    {
        $this->assertEquals(
            null,
            $this->subject->getAdmissionRequirements()
        );
    }

    /**
     * @test
     */
    public function setAdmissionRequirementsForAdmissionRequirementsSetsAdmissionRequirements()
    {
        $admissionRequirementsFixture = new \In2code\In2studyfinder\Domain\Model\AdmissionRequirements();
        $this->subject->setAdmissionRequirements($admissionRequirementsFixture);

        $this->assertAttributeEquals(
            $admissionRequirementsFixture,
            'admissionRequirements',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStartOfStudyReturnsInitialValueForStartOfStudy()
    {
        $this->assertEquals(
            null,
            $this->subject->getStartOfStudy()
        );
    }

    /**
     * @test
     */
    public function setStartOfStudyForStartOfStudySetsStartOfStudy()
    {
        $startOfStudyFixture = new \In2code\In2studyfinder\Domain\Model\StartOfStudy();
        $this->subject->setStartOfStudy($startOfStudyFixture);

        $this->assertAttributeEquals(
            $startOfStudyFixture,
            'startOfStudy',
            $this->subject
        );
    }
}
