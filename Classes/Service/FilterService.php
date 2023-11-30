<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourseInterface;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use In2code\In2studyfinder\Utility\FrontendUtility;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class FilterService extends AbstractService
{
    protected array $settings = [];

    protected array $filter = [];

    protected PluginService $pluginService;

    /**
     * FilterService constructor.
     */
    public function __construct(LoggerInterface $logger, PluginService $pluginService)
    {
        parent::__construct($logger);

        $this->settings = ExtensionUtility::getExtensionSettings('in2studyfinder');
        $this->pluginService = $pluginService;
    }

    public function initialize(): void
    {
        $this->disableFilterFrontendRenderingByPluginRestrictions();
        $this->buildFilter();
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * removes not allowed keys and empty values from searchOptions
     */
    public function sanitizeSearch(array $searchOptions): array
    {
        // merge plugin restrictions to search options
        $searchOptions = array_merge($searchOptions, $this->getPluginFilterRestrictions());
        $this->disableFilterFrontendRenderingByPluginRestrictions();

        $filter = $this->getFilter();
        // remove not allowed keys
        foreach (array_keys($searchOptions) as $filterName) {
            if (!array_key_exists($filterName, $filter)) {
                unset($searchOptions[$filterName]);
            }
        }

        // remove empty values
        foreach ($searchOptions as $optionName => $optionValue) {
            if (empty($optionValue)) {
                unset($searchOptions[$optionName]);
            }
        }

        return $searchOptions;
    }

    /**
     * updates the filter keys to the actual property path
     */
    public function resolveFilterPropertyPath($searchOptions): array
    {
        $filter = $this->getFilter();

        foreach ($searchOptions as $filterName => $filterValues) {
            $searchOptions[$filter[$filterName]['propertyPath']] = $filterValues;
            if ($filter[$filterName]['propertyPath'] !== $filterName) {
                unset($searchOptions[$filterName]);
            }
        }

        return $searchOptions;
    }

    public function loadOrSetPersistedFilter(array $searchOptions): array
    {
        if (!empty($searchOptions)) {
            FrontendUtility::getTyposcriptFrontendController()
                ->fe_user
                ->setAndSaveSessionData('tx_in2studycourse_filter', array_filter($searchOptions));
        } else {
            $sessionData = FrontendUtility::getTyposcriptFrontendController()
                ->fe_user
                ->getSessionData('tx_in2studycourse_filter');
            if (!empty($sessionData)) {
                return (array)$sessionData;
            }
        }

        return $searchOptions;
    }

    public function setSettings(array $settings): FilterService
    {
        $this->settings = $settings;
        return $this;
    }

    private function getPluginFilterRestrictions(): array
    {
        if (array_key_exists('flexform', $this->settings)) {
            return $this->pluginService->preparePluginRestrictions(
                $this->settings['flexform']['select'] ?? [],
                $this->getFilter()
            );
        }

        return [];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function disableFilterFrontendRenderingByPluginRestrictions(): void
    {
        foreach ($this->getPluginFilterRestrictions() as $filterName => $values) {
            if (array_key_exists($filterName, $this->filter)) {
                $this->filter[$filterName]['disabledInFrontend'] = true;
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAvailableFilterOptions(array $studyCourses): array
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

    protected function buildFrontendLabel(array $filterConfiguration): string
    {
        $frontendLabel = LocalizationUtility::translate($filterConfiguration['frontendLabel'], 'in2studyfinder');
        if ($frontendLabel === null) {
            $frontendLabel = $filterConfiguration['frontendLabel'];
        }

        return $frontendLabel;
    }

    protected function isFilterInFrontendVisible(array $filterConfiguration): bool
    {
        return (bool)($filterConfiguration['disabledInFrontend'] ?? false);
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    protected function buildObjectFilter(string $filterName, array $filterConfiguration): void
    {
        $repositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName(
            $filterConfiguration['objectModel']
        );

        if (class_exists($repositoryClassName)) {
            $defaultQuerySettings = GeneralUtility::makeInstance(QuerySettingsInterface::class);
            $defaultQuerySettings->setStoragePageIds([$this->settings['settingsPid']]);
            $defaultQuerySettings->setLanguageOverlayMode(true);

            $repository = GeneralUtility::makeInstance($repositoryClassName);
            $repository->setDefaultQuerySettings($defaultQuerySettings);

            $this->filter[$filterName]['repository'] = $repositoryClassName;
            $this->filter[$filterName]['filterOptions'] = $repository->findAll()->toArray();
        } else {
            $this->logger->warning(
                'The given repository class ("' . $repositoryClassName . '") for the filter: ' . $filterName . ' do not exist. This filter will be ignored!',
                [
                    'filterName' => $filterName,
                    'filterConfiguration' => $filterConfiguration,
                    'additionalInfo' => ['class' => self::class, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
            unset($this->filter[$filterName]);
        }
    }

    protected function buildFilter(): void
    {
        if (empty($this->settings['settingsPid'])) {
            $this->logger->error(
                'No plugin.tx_in2studyfinder.settings.settingsPid is set! This results in not appearing filter options in the frontend.',
                [
                    'additionalInfo' => ['class' => self::class, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
        }

        foreach ($this->getTypoScriptFilterConfiguration() as $filterName => $filterProperties) {
            if ($filterProperties['type'] && $filterProperties['propertyPath'] && $filterProperties['frontendLabel']) {
                $this->filter[$filterName] = [
                    'type' => $filterProperties['type'],
                    'propertyPath' => $filterProperties['propertyPath'],
                    'frontendLabel' => $this->buildFrontendLabel($filterProperties),
                    'disabledInFrontend' => $this->isFilterInFrontendVisible($filterProperties),
                    'singleSelect' => $filterProperties['singleSelect'] ?? '',
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
                        'additionalInfo' => ['class' => self::class, 'method' => __METHOD__, 'line' => __LINE__],
                    ]
                );
            }
        }
    }

    protected function getTypoScriptFilterConfiguration(): array
    {
        $filters = $this->settings['filters'] ?? [];

        if (is_array($filters) && !empty($filters)) {
            return $filters;
        }

        $this->logger->error(
            'No typoscript filter configuration found!',
            [
                'settings' => $this->settings,
                'additionalInfo' => ['class' => self::class, 'method' => __METHOD__, 'line' => __LINE__],
            ]
        );

        return [];
    }
}
