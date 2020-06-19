<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Filter\FilterTypes;

use In2code\In2studyfinder\Filter\AbstractFilter;
use In2code\In2studyfinder\Filter\ValueFilterInterface;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class DomainObjectFilter extends AbstractFilter implements ValueFilterInterface
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct()
    {
        parent::__construct();

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->settings = ExtensionUtility::getExtensionSettings('in2studyfinder');
    }

    /**
     * @var string
     */
    protected $className = '';

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return DomainObjectFilter
     */
    public function setClassName(string $className): DomainObjectFilter
    {
        $this->className = $className;
        return $this;
    }

    public function buildFilterOptions()
    {
        $fullQualifiedRepositoryClassName = '';

        if (method_exists($this, 'getClassName')) {
            $fullQualifiedRepositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName(
                $this->getClassName()
            );
        }

        if (class_exists($fullQualifiedRepositoryClassName)) {
            $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
            $defaultQuerySettings->setStoragePageIds([$this->settings['settingsPid']]);
            $defaultQuerySettings->setLanguageOverlayMode(true);
            $defaultQuerySettings->setLanguageMode('strict');

            $repository = $this->objectManager->get($fullQualifiedRepositoryClassName);
            $repository->setDefaultQuerySettings($defaultQuerySettings);


            $this->setFilterOptions($repository->findAll()->toArray());
        } else {
            $this->logger->warning(
                'The given repository class ("' . $fullQualifiedRepositoryClassName . '") for the filter: ' .
                $this->getFilterName() . ' do not exist. This filter will be ignored!',
                [
                    'filterName' => $this->getFilterName(),
                    'filter' => serialize($this),
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]
                ]
            );
        }
    }

    /**
     * @param array $filterConfiguration
     * @param string $filterName
     * @return bool
     */
    public function isFilterConfigurationValid(array &$filterConfiguration, string $filterName): bool
    {
        $isValid = parent::isFilterConfigurationValid($filterConfiguration, $filterName);

        if ($isValid) {
            if (array_key_exists('objectModel', $filterConfiguration) && !array_key_exists(
                    'className',
                    $filterConfiguration
                )) {
                $this->logger->warning(
                    'Deprecated filter configuration key "objectModel". Please use the key "className" instead. ' .
                    'This key is deprecated since in2studyfinder version 7.0 and will be removed in version 8.0!'
                );

                $filterConfiguration['className'] = $filterConfiguration['objectModel'];
            }

            if (!array_key_exists('className', $filterConfiguration)) {
                $isValid = false;
                // @todo log missing objectModel for filter
            } else {
                if (!class_exists($filterConfiguration['className'])) {
                    $isValid = false;
                    // @todo log not existing objectModel in filter configuration
                }
            }
        }

        return $isValid;
    }
}
