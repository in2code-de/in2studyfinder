<?php
namespace In2code\In2studyfinder\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * StartOfStudy
 */
class StartOfStudy extends AbstractEntity implements StartOfStudyInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_startofstudy';

    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * startDate
     *
     * @var string
     */
    protected $startDate = '';

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
     * Returns the startDate
     *
     * @return string $startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the startDate
     *
     * @param string $startDate
     * @return void
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField()
    {
        return $this->getTitle();
    }
}
