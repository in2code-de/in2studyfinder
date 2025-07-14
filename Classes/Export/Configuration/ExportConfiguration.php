<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Export\Configuration;

use In2code\In2studyfinder\Export\ExportInterface;
use TYPO3\CMS\Core\Core\Environment;

class ExportConfiguration
{
    protected array $propertiesToExport = [];

    protected bool $includeHidden = false;

    protected bool $includeDeleted = false;

    protected string $exportLocation = '';

    protected ExportInterface $exporter;

    protected array $recordsToExport = [];

    public function __construct()
    {
        $this->exportLocation = Environment::getPublicPath() . '/' . 'typo3temp/tx_in2studyfinder/';
    }

    public function getPropertiesToExport(): array
    {
        return $this->propertiesToExport;
    }

    public function setPropertiesToExport(array $propertiesToExport): ExportConfiguration
    {
        $this->propertiesToExport = $propertiesToExport;
        return $this;
    }

    public function setExporter(ExportInterface $exporter): ExportConfiguration
    {
        $this->exporter = $exporter;
        return $this;
    }

    public function getExporter(): ExportInterface
    {
        return $this->exporter;
    }

    public function setRecordsToExport(array $recordsToExport): ExportConfiguration
    {
        $this->recordsToExport = $recordsToExport;
        return $this;
    }

    public function getRecordsToExport(): array
    {
        return $this->recordsToExport;
    }

    public function isIncludeHidden(): bool
    {
        return $this->includeHidden;
    }

    public function setIncludeHidden(bool $includeHidden): ExportConfiguration
    {
        $this->includeHidden = $includeHidden;
        return $this;
    }

    public function isIncludeDeleted(): bool
    {
        return $this->includeDeleted;
    }

    public function setIncludeDeleted(bool $includeDeleted): ExportConfiguration
    {
        $this->includeDeleted = $includeDeleted;
        return $this;
    }

    public function getExportLocation(): string
    {
        return $this->exportLocation;
    }

    public function setExportLocation(string $exportLocation): ExportConfiguration
    {
        $this->exportLocation = $exportLocation;
        return $this;
    }
}
