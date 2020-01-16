<?php
declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class FilterService extends AbstractService
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * FilterService constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->settings = ExtensionUtility::getExtensionSettings('in2studyfinder');
        $this->setFilter();
    }

    /**
     * returns possibly defined filter restrictions in the plugin
     *
     * @return array
     */
    public function getPluginFilterRestrictions()
    {
        $selectedPluginFilter = [];
        if (!empty($this->settings['flexform']['select'])) {
            foreach ($this->settings['flexform']['select'] as $filterName => $uid) {
                if (array_key_exists($filterName, $this->filter) && $uid !== '') {
                    $selectedPluginFilter[$filterName] = GeneralUtility::intExplode(',', $uid, true);
                } else {
                    $this->logger->info(
                        'Remove the plugin filter restriction for filter: "' . $filterName . '". Because the given restriction is not defined in the typoscript filter section.',
                        ['additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]]
                    );
                }
            }
        }

        return $selectedPluginFilter;
    }

    /**
     * get configured filter which also not disabled in the frontend
     *
     * @return array
     */
    public function getEnabledFrontendFilter()
    {
        $filters = [];
        $pluginFilterRestrictions = $this->getPluginFilterRestrictions();

        foreach ($this->filter as $filterName => $filter) {
            // disable filters in the frontend if the same filter is set in the backend plugin
            if (array_key_exists($filterName, $pluginFilterRestrictions)
                && $pluginFilterRestrictions[$filterName] !== '') {
                $filter['disabledInFrontend'] = true;
            }
            if ($filter['disabledInFrontend'] === false) {
                $filters[$filterName] = $filter;
            }
        }

        return $filters;
    }

    public function setFilter()
    {
        $this->buildFilter();
    }

    /**
     * returns the available filter options for an given array of courses
     *
     * @param array $studyCourses
     * @return array
     */
    public function getAvailableFilterOptions($studyCourses): array
    {
        $availableOptions = [];

        foreach ($this->filter as $filterName => $filter) {
            /** @var $studyCourse StudyCourseInterface */
            foreach ($studyCourses as $studyCourse) {
                $property = ObjectAccess::getPropertyPath($studyCourse, $filter['propertyPath']);

                switch ($filter['type']) {
                    case 'object':
                        if ($property instanceof ObjectStorage) {
                            foreach ($property as $obj) {
                                $availableOptions[$filterName][$obj->getUid()] = $obj->getUid();
                            }
                        } elseif ($property instanceof AbstractDomainObject) {
                            $availableOptions[$filterName][$property->getUid()] = $property->getUid();
                        }
                        break;
                    case 'boolean':
                        if ($property !== '' && $property !== 0 && $property !== false) {
                            $availableOptions[$filterName][0] = 'true';
                        } else {
                            $availableOptions[$filterName][1] = 'false';
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        return $availableOptions;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param array $filterConfiguration
     * @return string
     */
    protected function buildFrontendLabel(array $filterConfiguration): string
    {
        $frontendLabel = LocalizationUtility::translate($filterConfiguration['frontendLabel'], 'in2studyfinder');
        if ($frontendLabel === null) {
            $frontendLabel = $filterConfiguration['frontendLabel'];
        }

        return $frontendLabel;
    }

    /**
     * @param array $filterConfiguration
     * @return bool
     */
    protected function isFilterInFrontendVisible(array $filterConfiguration): bool
    {
        $disabledInFrontend = false;

        if ($filterConfiguration['disabledInFrontend'] === '1') {
            $disabledInFrontend = true;
        }

        return $disabledInFrontend;
    }

    /**
     * @param string $filterName
     * @param array $filterConfiguration
     */
    protected function buildObjectFilter(string $filterName, array $filterConfiguration)
    {
        $fullQualifiedRepositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName(
            $filterConfiguration['objectModel']
        );

        if (class_exists($fullQualifiedRepositoryClassName)) {
            $defaultQuerySettings = $this->objectManager->get(QuerySettingsInterface::class);
            $defaultQuerySettings->setStoragePageIds([$this->settings['settingsPid']]);
            $defaultQuerySettings->setLanguageOverlayMode(true);
            $defaultQuerySettings->setLanguageMode('strict');

            $repository = $this->objectManager->get($fullQualifiedRepositoryClassName);
            $repository->setDefaultQuerySettings($defaultQuerySettings);

            $this->filter[$filterName]['repository'] = $repository;
            $this->filter[$filterName]['filterOptions'] = $repository->findAll()->toArray();
        } else {
            $this->logger->warning(
                'The given repository class ("' . $fullQualifiedRepositoryClassName . '") for the filter: ' . $filterName . ' do not exist. This filter will be ignored!',
                [
                    'filterName' => $filterName,
                    'filterConfiguration' => $filterConfiguration,
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]
                ]
            );
            unset($this->filter[$filterName]);
        }
    }

    /**
     * builds the actual filter array
     */
    protected function buildFilter()
    {
        if (empty($this->settings['settingsPid'])) {
            $this->logger->error(
                'No plugin.tx_in2studyfinder.settings.settingsPid is set! This results in not appearing filter options in the frontend.',
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]
                ]
            );
        }

        foreach ((array)$this->getTypoScriptFilterConfiguration() as $filterName => $filterProperties) {
            if ($filterProperties['type'] && $filterProperties['propertyPath'] && $filterProperties['frontendLabel']) {

                $this->filter[$filterName] = [
                    'type' => $filterProperties['type'],
                    'propertyPath' => $filterProperties['propertyPath'],
                    'frontendLabel' => $this->buildFrontendLabel($filterProperties),
                    'disabledInFrontend' => $this->isFilterInFrontendVisible($filterProperties),
                ];

                switch ($filterProperties['type']) {
                    case 'object':
                        $this->buildObjectFilter($filterName, $filterProperties);
                        break;
                    case 'boolean':
                        $this->filter[$filterName]['filterOptions'] = [true, false];
                        break;
                    default:
                        break;
                }
            } else {
                $this->logger->warning(
                    'The given filter configuration for the filter: ' . $filterName . ' is not valid. This filter will be ignored!',
                    [
                        'filterName' => $filterName,
                        'filterProperties' => $filterProperties,
                        'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]
                    ]
                );
            }
        }
    }

    /**
     * @return array
     */
    protected function getTypoScriptFilterConfiguration(): array
    {
        if (is_array($this->settings['filters']) && !empty($this->settings['filters'])) {
            return $this->settings['filters'];
        } else {
            $this->logger->error(
                'No typoscript filter configuration found!',
                [
                    'settings' => $this->settings,
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__]
                ]
            );
        }

        return [];
    }
}
