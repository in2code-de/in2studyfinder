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

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for StudyCourses
 */
class AbstractRepository extends Repository
{
    // Example for repository wide settings
    public function initializeObject() {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        // go for $defaultQuerySettings = $this->createQuery()->getQuerySettings(); if you want to make use of the TS persistence.storagePid with defaultQuerySettings(), see #51529 for details

        // don't add the pid constraint
        $querySettings->setRespectStoragePage(FALSE);
        // set the storagePids to respect
        $querySettings->setStoragePageIds(array(6));

        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function optionsToConstraints($options = array())
    {
        $query = $this->createQuery();
        $constraints = array();
        foreach ($options as $name => $uidArray) {
            $constraints[] = $query->in($name . '.uid', $uidArray);
        }
        return $constraints;
    }
}


