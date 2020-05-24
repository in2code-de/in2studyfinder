<?php

namespace In2code\In2studyfinder\Export\Configuration;

use In2code\In2studyfinder\Export\ExportInterface;
use TYPO3\CMS\Core\Core\Environment;

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
    protected $exportLocation = '';

    /**
     * @var ExportInterface
     */
    protected $exporter = null;

    /**
     * @var array
     */
    protected $recordsToExport = [];

    public function __construct()
    {
        $this->exportLocation = Environment::getPublicPath() . '/' . 'typo3temp/tx_in2studyfinder/';
    }

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
    public function setRecordsToExport(array $recordsToExport)
    {
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
