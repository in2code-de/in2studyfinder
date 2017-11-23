<?php

namespace In2code\In2studyfinder\Domain\Service;

use In2code\In2studyfinder\DataProvider\ExportConfiguration;
use In2code\In2studyfinder\DataProvider\ExportProviderInterface;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ExportService
{
    /**
     * @var ExportProviderInterface
     */
    protected $exporter = null;

    /**
     * @var ExportConfiguration
     */
    protected $exportConfiguration = null;

    /**
     * @var array
     */
    protected $exportRecords = [];

    public function __construct()
    {
    }

    public function export()
    {
        $exportRecords = [];

        foreach ($this->getExportRecords() as $record) {
            $exportRecords[] = $this->getFieldsForExportFromRecord($record);
        }

        $this->exporter->export($exportRecords, $this->exportConfiguration);
    }

    /**
     * @param AbstractDomainObject $record
     *
     * @return array
     */
    protected function getFieldsForExportFromRecord($record)
    {
        $exportData = [];

        foreach ($this->exportConfiguration->getFields() as $field) {
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
     * @param ExportProviderInterface $exporter
     */
    public function setExporter(
        ExportProviderInterface $exporter
    ) {
        $this->exporter = $exporter;
    }

    /**
     * @return ExportProviderInterface
     */
    public function getExporter()
    {
        return $this->exporter;
    }


    /**
     * @param array $exportRecords
     */
    public function setExportRecords(
        array $exportRecords
    ) {
        $this->exportRecords = $exportRecords;
    }

    /**
     * @return array
     */
    public function getExportRecords()
    {
        return $this->exportRecords;
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
