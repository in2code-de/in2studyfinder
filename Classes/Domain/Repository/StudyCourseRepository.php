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

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The repository for StudyCourses
 */
class StudyCourseRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @param $options
     * @return QueryResultInterface
     */
    public function findAllFilteredByOptions($options)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setLanguageOverlayMode(true);
        $query->getQuerySettings()->setLanguageMode('strict');

        /**
         * Add the Storage Pid for Settings
         */
        $storagePids = $query->getQuerySettings()->getStoragePageIds();
        $settings = ExtensionUtility::getExtensionSettings('in2studyfinder');

        if (!in_array((int)$settings['settingsPid'], $storagePids)) {
            array_push($storagePids, $settings['settingsPid']);
        }

        $query->getQuerySettings()->setStoragePageIds($storagePids);

        $constraints = [];
        foreach ($options as $name => $array) {
            if ($array[0] === 'true') {
                $constraints[] = $query->logicalOr(
                    [
                        $query->logicalNot($query->equals($name, '')),
                        $query->greaterThan($name, 0),
                    ]
                );
            } elseif ($array[0] === 'false') {
                $constraints[] = $query->logicalOr(
                    [
                        $query->equals($name, 0),
                        $query->equals($name, ''),
                        $query->equals($name, null),
                    ]
                );
            } else {
                $constraints[] = $query->in($name . '.uid', $array);
            }
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute();
    }
}
