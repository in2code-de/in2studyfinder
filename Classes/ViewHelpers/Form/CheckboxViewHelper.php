<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers\Form;

class CheckboxViewHelper extends AbstractCheckboxViewHelper
{
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
                $checked = in_array($valueAttribute, $propertyValue);
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
}
