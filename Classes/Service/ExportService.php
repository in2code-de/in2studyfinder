<?php

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;
use In2code\In2studyfinder\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ExportService extends AbstractService
{
    /**
     * @var ExportConfiguration
     */
    protected $exportConfiguration = null;

    /**
     * ExportService constructor.
     *
     * @param string $exporterType
     * @param array $selectedProperties
     * @param array $courses
     */
    public function __construct($exporterType, array $selectedProperties, array $courses)
    {
        /** @var ExportInterface $exporter */
        $exporter = GeneralUtility::makeInstance($exporterType);
        $this->setExportConfiguration(GeneralUtility::makeInstance(ExportConfiguration::class));

        $this->exportConfiguration
            ->setPropertiesToExport($selectedProperties)
            ->setExporter($exporter);

        $this->exportConfiguration->setRecordsToExport($this->prepareRecordsForExport($courses));
    }

    /**
     * @return void filename of the exported file
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function export()
    {
        $fullQualifiedFileName = $this->exportConfiguration->getExporter()->export($this->exportConfiguration);
        $filename = FileUtility::getFilenameFromFileWithPath($fullQualifiedFileName);

        $headers = [
            'Pragma' => 'public',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'text/plain',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length' => filesize($fullQualifiedFileName)
        ];

        foreach ($headers as $header => $data) {
            header($header . ': ' . $data);
        }

        @readfile($fullQualifiedFileName);
        exit;
    }

    /**
     * @param array $courses
     * @return array
     */
    public function prepareRecordsForExport($courses)
    {
        $processedRecords = [];

        foreach ($courses as $record) {
            $processedRecords[] = $this->getFieldsForExportFromRecord($record);
        }

        return $processedRecords;
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
     * @param string $record
     * @param array $propertyPath
     * @return string
     */
    protected function getPropertyByPropertyPath($record, array $propertyPath)
    {
        foreach ($propertyPath as $propertyName) {
            if ($record instanceof ObjectStorage) {
                $recordArray = $record->toArray();
                $value = '';
                foreach ($recordArray as $item) {
                    /** @var $item DomainObjectInterface */
                    $value .= $item->_getProperty($propertyName) . ', ';
                }

                $value = trim($value, ', ');

                $record = $value;
            } else {
                if ($record !== null) {
                    /** @var $record DomainObjectInterface */
                    $record = $record->_getProperty($propertyName);
                }
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
     * @param string $property
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
