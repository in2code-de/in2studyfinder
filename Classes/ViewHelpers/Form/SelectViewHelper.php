<?php

namespace In2code\In2studyfinder\ViewHelpers\Form;

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class SelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    public function initializeArguments()
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
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    protected function getOptions()
    {
        $parentOptions = parent::getOptions();
        $options = [];
        $optionsArgument = $this->arguments['options'];
        $settings = ExtensionUtility::getExtensionSettings('in2studyfinder');
        $pageUid = $settings['flexform']['studyCourseDetailPage'];

        if ($this->hasArgument('detailPageUid')) {
            $pageUid = $this->arguments['detailPageUid'];
        }

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = $objectManager->get(UriBuilder::class);

        foreach ($optionsArgument as $value) {
            if ($this->hasArgument('additionalOptionAttributes')) {
                if ($value instanceof AbstractDomainObject) {
                    $optionsArrayKey = get_class($value) . ':' . $value->getUid();
                    $label = '';

                    /*
                     * add label from parent options array to the new options array
                     * there are different array keys in the parent options e.g
                     *
                     * [1] in typo3 8.7
                     * [In2code\In2studyfinder\Domain\Model\StudyCourse:1] in typo3 6.2 and 7.6
                     *
                    */
                    // Typo3 8.7
                    if (array_key_exists($value->getUid(), $parentOptions)) {
                        $label = $parentOptions[$value->getUid()];
                    }
                    // Typo3 6.2 and 7.6
                    if (array_key_exists($optionsArrayKey, $parentOptions)) {
                        $label = $parentOptions[$optionsArrayKey];
                    }
                    $options[$optionsArrayKey]['label'] = $label;

                    foreach ($this->arguments['additionalOptionAttributes'] as $attribute => $property) {
                        $options[$optionsArrayKey]['additionalAttributes'][$attribute] =
                            \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath(
                                $value,
                                $property
                            );

                        $uri = $uriBuilder->reset()->setTargetPageUid($pageUid)->uriFor(
                            'detail',
                            ['studyCourse' => $value],
                            'StudyCourse',
                            'in2studyfinder',
                            'Pi2'
                        );

                        $options[$optionsArrayKey]['additionalAttributes']['data-url'] = $uri;
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Render the option tags.
     *
     * @param array $options the options for the form.
     * @return string rendered tags.
     */
    protected function renderOptionTags($options)
    {
        $output = '';

        // prepended option
        if ($this->hasArgument('prependOptionLabel')) {
            $value = $this->hasArgument('prependOptionValue') ? $this->arguments['prependOptionValue'] : '';
            $label = $this->arguments['prependOptionLabel'];

            $additionalAttributes = [];
            if (!empty($options[$value]['additionalAttributes'])) {
                $additionalAttributes = $options[$value]['additionalAttributes'];
            }

            $output .= $this->renderOptionTag($value, $label, false, $additionalAttributes) . LF;
        }

        // options from option attribute
        foreach ($options as $value => $attributes) {
            if ('' === $value && '' === $attributes) {
                continue;
            }
            $isSelected = $this->isSelected($value);

            $additionalAttributes = [];
            if (!empty($attributes['additionalAttributes'])) {
                $additionalAttributes = $attributes['additionalAttributes'];
            }

            $output .= $this->renderOptionTag($value, $attributes['label'], $isSelected, $additionalAttributes) . LF;
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
    protected function renderOptionTag($value, $label, $isSelected, $additionalAttributes = [])
    {
        $output =
            '<option value="' . htmlspecialchars($value) . '" ' . $this->getAdditionalAttributesString(
                $additionalAttributes
            );
        if ($isSelected) {
            $output .= ' selected="selected"';
        }
        $output .= '>' . htmlspecialchars($label) . '</option>';
        return $output;
    }

    /**
     * @param array $additionalAttributes
     * @return string
     */
    protected function getAdditionalAttributesString($additionalAttributes)
    {

        $output = '';

        if (!empty($additionalAttributes)) {
            foreach ($additionalAttributes as $attribute => $value) {
                $output .= htmlspecialchars($attribute) . '="' . htmlspecialchars($value) . '"';
            }
        }

        return $output;
    }
}
