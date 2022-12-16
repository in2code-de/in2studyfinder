<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

class CheckboxViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'input';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerTagAttribute(
            'disabled',
            'string',
            'Specifies that the input element should be disabled when the page loads'
        );
        $this->registerArgument(
            'errorClass',
            'string',
            'CSS class to set if there are errors for this ViewHelper',
            false,
            'f3-form-error'
        );
        $this->overrideArgument('value', 'string', 'Value of input tag. Required for checkboxes', true);
        $this->registerUniversalTagAttributes();
        $this->registerArgument('checked', 'bool', 'Specifies that the input element should be preselected');
        $this->registerArgument('multiple', 'bool', 'Specifies whether this checkbox belongs to a multivalue (is part of a checkbox group)', false, false);
        $this->registerArgument(
            'possibleFilters',
            'array',
            'Array which holds "propertyType" => array("uid", ...)" for each available filter option',
            true,
            []
        );
        $this->registerArgument(
            'searchedOptions',
            'array',
            'Array of the previously selected filter options',
            true,
            []
        );
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(): string
    {
        $checked = $this->arguments['checked'];
        $multiple = $this->arguments['multiple'];

        $this->tag->addAttribute('type', 'checkbox');

        $nameAttribute = $this->getName();
        $valueAttribute = $this->getValueAttribute();
        $propertyValue = null;
        if ($this->hasMappingErrorOccurred()) {
            $propertyValue = $this->getLastSubmittedFormData();
        }
        if ($checked === null && $propertyValue === null) {
            $propertyValue = $this->getPropertyValue();
        }

        if ($propertyValue instanceof \Traversable) {
            $propertyValue = iterator_to_array($propertyValue);
        }
        if (is_array($propertyValue)) {
            $propertyValue = array_map([$this, 'convertToPlainValue'], $propertyValue);
            if ($checked === null) {
                $checked = in_array($valueAttribute, $propertyValue, true);
            }
            $nameAttribute .= '[]';
        } elseif ($multiple === true) {
            $nameAttribute .= '[]';
        } elseif ($propertyValue !== null) {
            $checked = (bool)$propertyValue === (bool)$valueAttribute;
        }

        $this->registerFieldNameForFormTokenGeneration($nameAttribute);
        $this->tag->addAttribute('name', $nameAttribute);
        $this->tag->addAttribute('value', $valueAttribute);
        if ($checked === true) {
            $this->tag->addAttribute('checked', 'checked');
        }

        $this->setDisabledIfNotAvailable();
        $this->setSelectedIfPreviouslySelected();

        $this->setErrorClassAttribute();
        $hiddenField = $this->renderHiddenFieldForEmptyValue();

        return $hiddenField . $this->tag->render();
    }

    protected function setDisabledIfNotAvailable(): void
    {
        [$propertyName, $objectId] = explode('_', $this->arguments['id']);

        if (is_array($this->arguments['possibleFilters']) && !empty($this->arguments['possibleFilters'])) {
            if (
                !isset($this->arguments['possibleFilters'][$propertyName]) ||
                !in_array($objectId, $this->arguments['possibleFilters'][$propertyName], true)
            ) {
                $this->tag->addAttribute('disabled', 'disabled');
            }
        }
    }

    protected function setSelectedIfPreviouslySelected(): void
    {
        [$propertyName, $objectId] = explode('_', $this->arguments['id']);
        if (isset($this->arguments['searchedOptions'][$propertyName])) {
            if (in_array($objectId, $this->arguments['searchedOptions'][$propertyName], true)) {
                $this->tag->addAttribute('checked', true);
            }
        }
    }
}
