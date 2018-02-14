<?php

namespace In2code\In2studyfinder\Export\Configuration;

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

use In2code\In2studyfinder\Export\ExportInterface;

/**
 * ExportConfiguration
 */
class ExportConfiguration
{
    /**
     * @var array
     */
    protected $propertiesToExport = [];

    /**
     * @var bool
     */
    protected $includeHidden = false;

    /**
     * @var bool
     */
    protected $includeDeleted = false;

    /**
     * @var string
     */
    protected $exportLocation = 'fileadmin/';

    /**
     * @var ExportInterface
     */
    protected $exporter = null;

    /**
     * @var array
     */
    protected $recordsToExport = [];

    /**
     * @return array
     */
    public function getPropertiesToExport()
    {
        return $this->propertiesToExport;
    }

    /**
     * @param array $propertiesToExport
     *
     * @return $this
     */
    public function setPropertiesToExport($propertiesToExport)
    {
        $this->propertiesToExport = $propertiesToExport;
        return $this;
    }

    /**
     * @param ExportInterface $exporter
     *
     * @return $this
     */
    public function setExporter(ExportInterface $exporter)
    {
        $this->exporter = $exporter;
        return $this;
    }

    /**
     * @return ExportInterface
     */
    public function getExporter()
    {
        return $this->exporter;
    }

    /**
     * @param array $recordsToExport
     *
     * @return $this
     */
    public function setRecordsToExport(array $recordsToExport) {
        $this->recordsToExport = $recordsToExport;
        return $this;
    }

    /**
     * @return array
     */
    public function getRecordsToExport()
    {
        return $this->recordsToExport;
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
     *
     * @return $this
     */
    public function setIncludeHidden($includeHidden)
    {
        $this->includeHidden = $includeHidden;
        return $this;
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
     *
     * @return $this
     */
    public function setIncludeDeleted($includeDeleted)
    {
        $this->includeDeleted = $includeDeleted;
        return $this;
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
     *
     * @return $this
     */
    public function setExportLocation($exportLocation)
    {
        $this->exportLocation = $exportLocation;
        return $this;
    }
}
