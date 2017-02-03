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

use In2code\In2studyfinder\Domain\Model\GlobalData;
use In2code\In2studyfinder\Domain\Repository\GlobalDataRepository;

class GlobalDataUtility extends AbstractUtility
{

    /**
     * @param GlobalDataRepository
     * @return bool
     */
    public static function existDefaultPreset($globalDataRepository)
    {

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
     * @param GlobalDataRepository $globalDataRepositoryClass
     * @return GlobalData|null
     */
    public static function getDefaultPreset($globalDataRepositoryClass = GlobalDataRepository::class)
    {
        $defaultPreset = null;

        $globalDataRepository = self::getObjectManager()->get($globalDataRepositoryClass);
    
        if ($globalDataRepository instanceof GlobalDataRepository) {
            if (self::existDefaultPreset($globalDataRepository)) {
                $defaultPreset = $globalDataRepository->findDefaultPreset();
            }
        }

        return $defaultPreset;
    }
}

?>
