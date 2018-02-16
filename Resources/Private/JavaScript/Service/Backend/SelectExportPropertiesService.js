define(['TYPO3/CMS/In2studyfinder/Utility/UiUtility'], function(UiUtility) {
	'use strict';

	var SelectExportPropertiesService = {
		selectedPropertiesList: document.querySelector('.js-in2studyfinder-selected-properties-list'),
        availablePropertiesList: document.querySelector('.js-in2studyfinder-property-list')
	};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	SelectExportPropertiesService.initialize = function() {
		SelectExportPropertiesService.addEventListener();
	};

	/**
	 *
	 */
	SelectExportPropertiesService.addEventListener = function() {

		// click on the remove item button

		// click on move up button

		// click on move down button

		// click on move to the end button

		// click on move to the beginning button

		// click on an available item
		var propertyList = document.querySelector('.js-in2studyfinder-property-list');
		propertyList.addEventListener('click', SelectExportPropertiesService.addPropertyToSelectedProperties);
	};

	/**
	 * copies the selected option element to the selected items list
	 *
	 * @param event
	 */
	SelectExportPropertiesService.addPropertyToSelectedProperties = function(event) {
        var targetOption = event.target;

        if (targetOption.getAttribute('data-in2studyfinder-property-selectable') === 'true') {
        	var copiedOption = targetOption.cloneNode(true);
            UiUtility.hideElement(targetOption);
        	UiUtility.removeStyles(copiedOption);

        	/*
        	 *	add parent element name
        	 *	this prevents multiple elements with the same label e.g. Title -> Department Title
        	 */
			if (copiedOption.getAttribute('data-in2studyfinder-parent-property-label')) {
                copiedOption.insertAdjacentHTML(
                	'afterbegin',
					copiedOption.getAttribute('data-in2studyfinder-parent-property-label')
				);
			}
            SelectExportPropertiesService.selectedPropertiesList.add(copiedOption);
        }
    };

	SelectExportPropertiesService.initialize();
	return SelectExportPropertiesService;
});
