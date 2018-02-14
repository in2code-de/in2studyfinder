<?php

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ExportService
{
    /**
     * @var ExportConfiguration
     */
    protected $exportConfiguration = null;

    public function __construct()
    {
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function export()
    {
        $exportRecords = [];

        if ($this->exportConfiguration === null) {
            throw new Exception('No export Configuration is set!');
        }

        foreach ($this->exportConfiguration->getRecordsToExport() as $record) {
            $exportRecords[] = $this->getFieldsForExportFromRecord($record);
        }

        return $this->exportConfiguration->getExporter()->export($exportRecords, $this->exportConfiguration);
    }

    /**
     * @param AbstractDomainObject $record
     *
     * @return array
     */
    protected function getFieldsForExportFromRecord($record)
    {
        $exportData = [];

        foreach ($this->exportConfiguration->getPropertiesToExport() as $field) {
            if ($this->isPropertyPath($field)) {
                $exportData[$field] = $this->getPropertyByPropertyPath($record, $this->propertyPathToArray($field));
            } else {
                $exportData[$field] = $record->_getProperty($field);
            }
        }

        return $exportData;
    }

    /**
     * @todo refactor
     *
     * @param $record
     * @param array $propertyPath
     * @return string
     */
    protected function getPropertyByPropertyPath($record, array $propertyPath)
    {
        foreach ($propertyPath as $propertyName) {
            if ($record instanceof ObjectStorage) {
                $recordArray =  $record->toArray();
                $value = '';
                foreach ($recordArray as $item) {
                    $value .= $item->_getProperty($propertyName) . ', ';
                }

                $value = trim($value, ', ');

                $record = $value;
            } else {
                $record = $record->_getProperty($propertyName);
            }
        }

        return $record;
    }

    /**
     * @param string $propertyPath
     * @return array
     */
    protected function propertyPathToArray($propertyPath)
    {
        return GeneralUtility::trimExplode('.', $propertyPath, true);
    }

    /**
     * @param AbstractDomainObject|ObjectStorage $record
     * @param $property
     * @return mixed
     * @throws \Exception
     */
    protected function getRecordProperty($record, $property)
    {
        try {
            return $record->_getProperty($property);
        } catch (\Exception $exception) {
            throw new \Exception('Property Path could not resolved"');
        }
    }

    /**
     * @param string $property
     * @return bool
     */
    protected function isPropertyPath(
        $property
    ) {
        if (strpos($property, '.') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @return ExportConfiguration
     */
    public function getExportConfiguration()
    {
        return $this->exportConfiguration;
    }

    /**
     * @param ExportConfiguration $exportConfiguration
     */
    public function setExportConfiguration(
        ExportConfiguration $exportConfiguration
    ) {
        $this->exportConfiguration = $exportConfiguration;
    }
}
