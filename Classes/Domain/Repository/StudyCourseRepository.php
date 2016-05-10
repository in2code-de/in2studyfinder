<?php
namespace In2code\In2studyfinder\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for StudyCourses
 */
class StudyCourseRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    protected $filterToStudyCoursePropertyMappingArray = [
        'academicDegree' => 'academicDegree',
        'admissionRequirement' => 'admissionRequirements',
        'courseLanguage' => 'courseLanguages',
        'department' => 'department',
        'faculty' => 'faculty',
        'startOfStudy' => 'startsOfStudy',
        'typeOfStudy' => 'typesOfStudy',
    ];

    /**
     * @param $options
     * @return array
     */
    protected function mapOptionsToStudyCourseProperties($options)
    {
        $mappedOptions = [];

        foreach ($options as $key => $value) {
            $mappedOptions[$this->filterToStudyCoursePropertyMappingArray[$key]] = $value;
        }

        return $mappedOptions;
    }

    /**
     * @param $options
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllFilteredByOptions($options)
    {
        $query = $this->createQuery();

        /*
         * Workaround für Extbase Language handling.
         *
         * Das Problem:
         * Übersetzte Datensätze werden über die Filterung nicht gefunden weil Extbase
         * die Werte wie academic_degree usw. zwar im übersetzten Datensatz sucht,
         * aber nicht findet, da sie dort nicht gepflegt werden (l10n_mode:exclude)
         *
         * Die Lösung:
         * setRespectSysLanguage(FALSE) sorgt dafür, dass alle Datensätze gefunden
         * werden, die diese Eigenschaften haben. In diesem Falle eigentlich nur
         * die Originalsprachigen. Diese werden später von
         * doWorkspaceAndLanguageOverlay() in die übersetzen Datensätze umgewandelt.
         * Damit keine Originalsprachigen Datensätze angezeigt werden, wird der
         * LanguageOverlayMode auf strict gesetzt. Dadurch verwirft
         * doWorkspaceAndLanguageOverlay() originalsprachige Datensätze die keine
         * Übersetzung haben.
         *
         * Gut zu wissen:
         * Es ist grundsätzlich egal, welcher Wert bei dem übersetzten Datensatz
         * eingetragen ist, wenn der Originalsprachige Datensatz den Suchwerten
         * entspricht.
         * Wenn der Übersetzte Datensatz den Suchwerten entspricht, der
         * Originalsprachige aber nicht, dann wird er trotzdem gefunden.
         * Die meisten dieser Werte sind aber nicht im Backend pflegbar und sollten
         * immer auf 0 (null) stehen.
         *
         */
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setLanguageOverlayMode('strict');

        $mappedOptions = $this->mapOptionsToStudyCourseProperties($options);

        $constraints = $this->optionsToConstraints($mappedOptions);

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute();
    }
}
