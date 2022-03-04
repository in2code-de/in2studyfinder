<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;
use In2code\In2studyfinder\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ExportService
{
    protected ?ExportConfiguration $exportConfiguration = null;

    public function __construct(string $exporterType, array $selectedProperties, array $courses)
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
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function export(): void
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

    public function prepareRecordsForExport(array $courses): array
    {
        $processedRecords = [];

        foreach ($courses as $record) {
            $processedRecords[] = $this->getFieldsForExportFromRecord($record);
        }

        return $processedRecords;
    }

    protected function getFieldsForExportFromRecord(AbstractDomainObject $record): array
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

    protected function getPropertyByPropertyPath(AbstractDomainObject $record, array $propertyPath): string
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

    protected function propertyPathToArray(string $propertyPath): array
    {
        return GeneralUtility::trimExplode('.', $propertyPath, true);
    }

    protected function getRecordProperty($record, string $property)
    {
        try {
            return $record->_getProperty($property);
        } catch (\Exception $exception) {
            throw new \Exception('Property Path could not resolved"');
        }
    }

    protected function isPropertyPath(string $property): bool
    {
        return strpos($property, '.') !== false;
    }

    public function getExportConfiguration(): ExportConfiguration
    {
        return $this->exportConfiguration;
    }

    public function setExportConfiguration(ExportConfiguration $exportConfiguration)
    {
        $this->exportConfiguration = $exportConfiguration;
    }
}
