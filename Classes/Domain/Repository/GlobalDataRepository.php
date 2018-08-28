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

use In2code\In2studyfinder\Domain\Model\GlobalData;
use In2code\In2studyfinder\Domain\Model\GlobalDataInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for StudyCourses
 */
class GlobalDataRepository extends AbstractRepository
{
    /**
     * @return GlobalDataInterface|null|object
     */
    public function findDefaultPreset()
    {
        return $this->getDefaultQuery()->execute()->getFirst();
    }

    /**
     * @return int
     */
    public function countDefaultPreset()
    {
        return $this->getDefaultQuery()->execute()->count();
    }

    /**
     * @return QueryInterface
     */
    protected function getDefaultQuery()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                array(
                    $query->equals('default_preset', true),
                    $query->equals('deleted', 0),
                )
            )
        );
        return $query;
    }
}
