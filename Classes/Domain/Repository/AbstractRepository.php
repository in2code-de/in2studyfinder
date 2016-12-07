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
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for StudyCourses
 */
class AbstractRepository extends Repository
{

    public function initializeObject() {
        $this->setQuerySettings();
    }

    /**
     * Set Query Settings
     */
    protected function setQuerySettings() {
        /** @var $querySettings Typo3QuerySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $settings = ExtensionUtility::getExtensionSettings('in2studyfinder');

        $querySettings->setStoragePageIds(
            [
                $settings['settingsPid'],
                $settings['storagePid']
            ]
        );

        $querySettings->setLanguageOverlayMode(true);
        $querySettings->setLanguageMode('strict');

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


