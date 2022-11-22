<?php
namespace In2code\In2studyfinder\Tests\Unit\Controller;

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
 * Test case for class In2code\In2studyfinder\Controller\StudyCourseController.
 *
 * @author Sebastian Stein <sebastian.stein@in2code.de>
 */
class StudyCourseControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \In2code\In2studyfinder\Controller\StudyCourseController
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = $this->getMock(
            \In2code\In2studyfinder\Controller\StudyCourseController::class,
            ['redirect', 'forward', 'addFlashMessage'],
            [],
            '',
            false
        );
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function listActionFetchesAllStudyCoursesFromRepositoryAndAssignsThemToView()
    {

        $allStudyCourses = $this->getMock(
            'TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage',
            [],
            [],
            '',
            false
        );

        $studyCourseRepository = $this->getMock(
            \In2code\In2studyfinder\Domain\Repository\StudyCourseRepository::class,
            ['findAll'],
            [],
            '',
            false
        );
        $studyCourseRepository->expects($this->once())->method('findAll')->will($this->returnValue($allStudyCourses));
        $this->inject($this->subject, 'studyCourseRepository', $studyCourseRepository);

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $view->expects($this->once())->method('assign')->with('studyCourses', $allStudyCourses);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenStudyCourseToView()
    {
        $studyCourse = new \In2code\In2studyfinder\Domain\Model\StudyCourse();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('studyCourse', $studyCourse);

        $this->subject->showAction($studyCourse);
    }
}
