<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers\Form;

use In2code\In2studyfinder\Settings\ExtensionSettingsInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * @todo refactor
 *
 * this is currently the default select viewHelper with additional functionality
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class SelectViewHelper extends AbstractSelectViewHelper
{
    /**
     * Constructor
     */
    public function __construct(private readonly \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder, private readonly ExtensionSettingsInterface $extensionSettings)
    {
        parent::__construct();
    }
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'additionalOptionAttributes',
            'array',
            'Array which holds "propertyType" => array("uid", ...)" for each option'
        );

        $this->registerArgument(
            'detailPageUid',
            'string',
            'the uid of the of the detail page'
        );

        $this->registerArgument(
            'action',
            'string',
            'the target action of the select (fallback is detail)'
        );
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getOptions(): array
    {
        $originalOptions = parent::getOptions();
        $updatedOptions = [];
        $optionsArgument = $this->arguments['options'];
        $settings = $this->extensionSettings->getTypoScriptSettings();
        if (ArrayUtility::isValidPath($settings, 'flexform/studyCourseDetailPage')) {
            $pageUid = $settings['flexform']['studyCourseDetailPage'];
        }

        if ($optionsArgument instanceof QueryResultInterface) {
            $optionsArgument = $optionsArgument->toArray();
        }

        $action = $this->hasArgument('action') ? $this->arguments['action'] : 'detail';

        if ($this->hasArgument('detailPageUid')) {
            $pageUid = $this->arguments['detailPageUid'];
        }

        $uriBuilder = $this->uriBuilder;

        foreach ($optionsArgument as $value) {
            if (!$this->hasArgument('additionalOptionAttributes')) {
                continue;
            }
            if (!$value instanceof AbstractDomainObject) {
                continue;
            }
            $label = '';
            if (array_key_exists($value->getUid(), $originalOptions)) {
                $label = $originalOptions[$value->getUid()];
            }
            $updatedOptions[$value->getUid()]['label'] = $label;
            foreach ($this->arguments['additionalOptionAttributes'] as $attribute => $property) {
                $updatedOptions[$value->getUid()]['additionalAttributes'][$attribute] =
                    \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath(
                        $value,
                        $property
                    );

                $uri =
                    $uriBuilder->reset()->setRequest($this->getRequest());

                if (!empty($pageUid)) {
                    $uri->setTargetPageUid((int)$pageUid);
                }

                $uri->uriFor(
                    $action,
                    ['studyCourse' => $value]
                );

                $updatedOptions[$value->getUid()]['additionalAttributes']['data-url'] = $uri->build();
            }
        }

        return $updatedOptions;
    }

    protected function renderOptionTags($options): string
    {
        $output = '';

        // options from option attribute
        foreach ($options as $value => $attributes) {
            if ('' === $value && empty($attributes)) {
                continue;
            }

            $isSelected = $this->isSelected($value);

            $additionalAttributes = [];
            if (!empty($attributes['additionalAttributes'])) {
                $additionalAttributes = $attributes['additionalAttributes'];
            }

            $output .= $this->renderOptionTag((string)$value, $attributes['label'], $isSelected, $additionalAttributes)
                . LF;
        }

        return $output;
    }

    /**
     * Render one option tag
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     * @param bool $isSelected specifies wheter or not to add selected attribute
     * @param array $additionalAttributes array with additional attributes
     * @return string the rendered option tag
     */
    protected function renderOptionTag(
        string $value,
        string $label,
        bool $isSelected,
        array $additionalAttributes = []
    ): string {
        $output =
            '<option value="' . htmlspecialchars($value) . '" ' . $this->getAdditionalAttributesString(
                $additionalAttributes
            );
        if ($isSelected) {
            $output .= ' selected="selected"';
        }
        return $output . ('>' . htmlspecialchars($label) . '</option>');
    }

    protected function getAdditionalAttributesString(array $additionalAttributes): string
    {
        $output = '';

        foreach ($additionalAttributes as $attribute => $value) {
            $output .= ' ' . htmlspecialchars($attribute) . '="' . htmlspecialchars((string) $value) . '"';
        }

        return ltrim($output);
    }
}
