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

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use In2code\In2studyfinderExtend\Domain\Model\StudyCourse as StudyCourseExtend;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Lang\Domain\Model\Extension;

/**
 * The repository for StudyCourses
 */
class StudyCourseRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    protected function getPropertyMapping(
        $properties,
        &$mapping,
        $currentLevel = 0,
        $parentElement = null
    ) {
        $settings = ExtensionUtility::getExtensionConfiguration('in2studyfinder');

        if ($currentLevel < $settings['filter']['recursive']) {
            foreach ($properties as $propertyName => $property) {
                if (is_object($property)) {
                    if ($property instanceof ObjectStorage) {
                        if ($property->current() !== null) {
                            $className = ExtensionUtility::getClassName($property->current());

                            $mapping[$className] = $propertyName;

                            $this->getPropertyMapping($property->current(), $mapping, $currentLevel + 1);

                        }

                    } elseif ($property instanceof AbstractDomainObject) {
                        $className = ExtensionUtility::getClassName($property);

                        $this->getPropertyMapping(
                            $property->_getProperties(), $mapping, $currentLevel + 1, $className
                        );


                        if ($className !== 'ttContent') {
                            if ($parentElement !== null) {
                                $propertyName = $parentElement . '.' . $propertyName;
                            }
                            $mapping[$className] = $propertyName;
                        }
                    }
                } else {
                    $mapping[$propertyName] = $propertyName;
                }
            }
        }
    }

    /**
     * @param $options
     * @return array
     */
    protected function mapOptionsToStudyCourseProperties($options)
    {

        if (ExtensionUtility::isIn2studycoursesExtendLoaded()) {
            $studyCourse = new StudyCourseExtend();
        } else {
            $studyCourse = new StudyCourse();
        }
        $filterToStudyCoursePropertyMappingArray = [];

        $this->getPropertyMapping($studyCourse->_getProperties(), $filterToStudyCoursePropertyMappingArray);

        $mappedOptions = [];

        foreach ($options as $key => $value) {
            $mappedOptions[$filterToStudyCoursePropertyMappingArray[$key]] = $value;
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

        $query->getQuerySettings()->setRespectSysLanguage(true);
        $query->getQuerySettings()->setLanguageOverlayMode(true);

        /**
         * Add the Storage Pid für Settings
         */
        $storagePids = $query->getQuerySettings()->getStoragePageIds();
        $settings = ExtensionUtility::getExtensionConfiguration('in2studyfinder');
        array_push($storagePids, $settings['settingsPid']);
        $query->getQuerySettings()->setStoragePageIds($storagePids);

        $mappedOptions = $this->mapOptionsToStudyCourseProperties($options);

        $constraints = array();
        foreach ($mappedOptions as $name => $array) {
            if ($array[0] === 'isSet') {
                $constraints[] = $query->logicalOr(
                    $query->logicalNot($query->equals($name, '')),
                    $query->greaterThan($name, 0)
                );
            } elseif ($array[0] === 'isUnset') {
                $constraints[] = $query->logicalOr(
                    $query->equals($name, 0),
                    $query->equals($name, ''),
                    $query->equals($name, null)
                );
            } else {
                $constraints[] = $query->in($name . '.uid', $array);
            }
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($query, __CLASS__ . ' in der Zeile ' . __LINE__);

        return $query->execute();
    }
}
