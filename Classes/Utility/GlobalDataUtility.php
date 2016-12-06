<?php

namespace In2code\In2studyfinder\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, In2code.de
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

use In2code\In2studyfinder\Domain\Repository\GlobalDataRepository;

class GlobalDataUtility
{

    /**
     * @return bool
     */
    public static function isGlobalDataHandlingEnabled()
    {
        $status = false;
        $settings = ExtensionUtility::getExtensionConfiguration('in2studyfinder');

        if ($settings['enableGlobalData']) {
            $status = true;
        }

        return $status;
    }

    /**
     * @return bool
     */
    public static function existDefaultPreset()
    {
        /** @var GlobalDataRepository $globalDataRepository */
        $globalDataRepository = ExtensionUtility::getObjectManager()->get(GlobalDataRepository::class);

        if ($globalDataRepository->countDefaultPreset() > 0) {
            $status = true;
        } else {
            /**
             * @todo Log No default_preset for tx_in2studyfinder_domain_model_globaldata defined!
             */
            $status = false;
        }

        return $status;
    }

    /**
     * @return \In2code\In2studyfinder\Domain\Model\GlobalData|null
     */
    public static function getDefaultPreset()
    {
        $defaultPreset = null;

        if (self::existDefaultPreset()) {
            /** @var GlobalDataRepository $globalDataRepository */
            $globalDataRepository = ExtensionUtility::getObjectManager()->get(GlobalDataRepository::class);
            $defaultPreset = $globalDataRepository->findDefaultPreset();
        }

        return $defaultPreset;
    }
}

?>
