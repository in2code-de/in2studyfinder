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
    protected $academicDegree = null;

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
