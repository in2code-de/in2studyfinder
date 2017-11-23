<?php

namespace In2code\In2studyfinder\DataProvider;

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

/**
 * ExportConfiguration
 */
class ExportConfiguration
{
    protected $fields = [];

    protected $includeHidden = false;

    protected $includeDeleted = false;

    protected $exportLocation = 'fileadmin/';

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return bool
     */
    public function isIncludeHidden()
    {
        return $this->includeHidden;
    }

    /**
     * @param bool $includeHidden
     */
    public function setIncludeHidden($includeHidden)
    {
        $this->includeHidden = $includeHidden;
    }

    /**
     * @return bool
     */
    public function isIncludeDeleted()
    {
        return $this->includeDeleted;
    }

    /**
     * @param bool $includeDeleted
     */
    public function setIncludeDeleted($includeDeleted)
    {
        $this->includeDeleted = $includeDeleted;
    }

    /**
     * @return string
     */
    public function getExportLocation()
    {
        return $this->exportLocation;
    }

    /**
     * @param string $exportLocation
     */
    public function setExportLocation($exportLocation)
    {
        $this->exportLocation = $exportLocation;
    }
}
