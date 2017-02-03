<?php
namespace In2code\In2studyfinder\ViewHelpers\Form;

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

class SelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('additionalOptionAttributes', 'array',
            'Array which holds "propertyType" => array("uid", ...)" for each option');
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
        $action = 'detail';
        $pageUid = $settings['flexform']['studyCourseDetailPage'];

        foreach ($parentOptions as $key => $value) {
            $options[$key]['label'] = $value;
        }
        $uriBuilder = $this->controllerContext->getUriBuilder();

        foreach ($optionsArgument as $value) {
            if ($this->hasArgument('additionalOptionAttributes')) {
                if ($value instanceof AbstractDomainObject) {
                    foreach ($this->arguments['additionalOptionAttributes'] as $attribute => $property) {
                        $options[$value->getUid()]['additionalAttributes'][$attribute] = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($value,
                            $property);

                        $uri = $uriBuilder->reset()->setTargetPageUid($pageUid)->uriFor($action,
                                ['studyCourse' => $value]);

                        $options[$value->getUid()]['additionalAttributes']['data-url'] = $uri;
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


        if ($this->hasArgument('prependOptionLabel')) {
            $value = $this->hasArgument('prependOptionValue') ? $this->arguments['prependOptionValue'] : '';
            $label = $this->arguments['prependOptionLabel'];
            $output .= $this->renderOptionTag($value, $label, false, $options[$value]['additionalAttributes']) . LF;
        }
        foreach ($options as $value => $attributes) {
            $isSelected = $this->isSelected($value);
            $output .= $this->renderOptionTag($value, $attributes['label'], $isSelected,
                    $attributes['additionalAttributes']) . LF;
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
        $output = '<option value="' . htmlspecialchars($value) . '" ' . $this->getAdditionalAttributesString($additionalAttributes);
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
