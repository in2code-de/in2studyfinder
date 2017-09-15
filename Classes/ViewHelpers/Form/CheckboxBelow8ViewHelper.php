<?php

namespace In2code\In2studyfinder\ViewHelpers\Form;

class CheckboxBelow8ViewHelper extends AbstractCheckboxViewHelper
{
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
        $valueAttribute = $this->getValueAttribute();
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
                if ($this->arguments['checked'] === null) {
                    $checked = in_array($valueAttribute, $propertyValue);
                }
                $nameAttribute .= '[]';
            } elseif (($multiple = false) === true) {
                $nameAttribute .= '[]';
            } elseif ($this->arguments['checked'] === null && $propertyValue !== null) {
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
}
