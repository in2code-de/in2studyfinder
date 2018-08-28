<?php
namespace In2code\In2studyfinder\ViewHelpers\Form;

abstract class AbstractCheckboxViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\CheckboxViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
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
     *
     */
    protected function setDisabledIfNotAvailable()
    {
        list($propertyName, $objectId) = explode('_', $this->arguments['id']);

        if (is_array($this->arguments['possibleFilters']) && !empty($this->arguments['possibleFilters'])) {
            if (!isset($this->arguments['possibleFilters'][$propertyName])
                || !in_array($objectId, $this->arguments['possibleFilters'][$propertyName])
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
