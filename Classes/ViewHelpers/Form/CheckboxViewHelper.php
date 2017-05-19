<?php

namespace In2code\In2studyfinder\ViewHelpers\Form;

class CheckboxViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\CheckboxViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'possibleFilters', 'array',
            'Array which holds "propertyType" => array("uid", ...)" for each available filter option', true, array()
        );
        $this->registerArgument(
            'searchedOptions', 'array', 'Array of the previously selected filter options', true, array()
        );
    }

    /**
     * Renders the checkbox.
     *
     * @param boolean $checked Specifies that the input element should be preselected
     * @param bool $multiple Specifies whether this checkbox belongs to a multivalue (is part of a checkbox group)
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     * @return string
     * @api
     */
    public function render($checked = null, $multiple = false)
    {
        $this->tag->addAttribute('type', 'checkbox');
        $nameAttribute = $this->getName();
        $valueAttribute = $this->getValue();
        if ($this->isObjectAccessorMode()) {
            if ($this->hasMappingErrorOccurred()) {
                $propertyValue = $this->getLastSubmittedFormData();
            } else {
                $propertyValue = $this->getPropertyValue();
            }

            if ($propertyValue instanceof \Traversable) {
                $propertyValue = iterator_to_array($propertyValue);
            }
            if (is_array($propertyValue)) {
                if ($checked === null) {
                    $checked = in_array($valueAttribute, $propertyValue);
                }
                $nameAttribute .= '[]';
            } elseif (($multiple = false) === true) {
                $nameAttribute .= '[]';
            } elseif ($checked === null && $propertyValue !== null) {
                $checked = (boolean)$propertyValue === (boolean)$valueAttribute;
            }
        }
        $this->registerFieldNameForFormTokenGeneration($nameAttribute);
        $this->tag->addAttribute('name', $nameAttribute);
        $this->tag->addAttribute('value', $valueAttribute);
        if ($checked) {
            $this->tag->addAttribute('checked', 'checked');
        }

        $this->setDisabledIfNotAvailable();
        $this->setSelectedIfPreviouslySelected();

        $this->setErrorClassAttribute();
        $hiddenField = $this->renderHiddenFieldForEmptyValue();

        return $hiddenField . $this->tag->render();
    }

    /**
     *
     */
    protected function setDisabledIfNotAvailable()
    {
        list($propertyName, $objectId) = explode('_', $this->arguments['id']);

        if (is_array($this->arguments['possibleFilters']) && !empty($this->arguments['possibleFilters'])) {
            if (!isset($this->arguments['possibleFilters'][$propertyName])
                || !in_array(
                    $objectId, $this->arguments['possibleFilters'][$propertyName]
                )
            ) {
                $this->tag->addAttribute('disabled', 'disabled');
            }
        }
    }

    /**
     *
     */
    protected function setSelectedIfPreviouslySelected()
    {
        list($propertyName, $objectId) = explode('_', $this->arguments['id']);
        if (isset($this->arguments['searchedOptions'][$propertyName])) {
            if (in_array($objectId, $this->arguments['searchedOptions'][$propertyName])) {
                $this->tag->addAttribute('checked', true);
            }
        }
    }
}
