define(['TYPO3/CMS/In2studyfinder/ExportModule'], function() {
	'use strict';

	var ExportModule = {
		selectedPropertiesCount: 0
	};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	ExportModule.initialize = function() {
		ExportModule.addEventListenerToPropertyList();
	};

	/**
	 *
	 */
	ExportModule.addEventListenerToPropertyList = function() {
		var propertyList = document.querySelector('.js-in2studyfinder-property-list');
		propertyList.addEventListener('click', ExportModule.togglePropertySelection);
	};

	/**
	 * @param event
	 */
	ExportModule.togglePropertySelection = function(event) {
		var selectedElement = event.target;
		if (selectedElement.getAttribute('data-in2studyfinder-is-selectable') === 'true') {
			if (selectedElement.getAttribute('data-in2studyfinder-is-selected') !== 'true') {
				ExportModule.addSelectionToProperty(selectedElement);
			} else {
				ExportModule.removeSelectionFromProperty(selectedElement);
			}
		}

		ExportModule.updateSelectedPropertiesCount();
	};

	/**
	 * @param element
	 */
	ExportModule.addSelectionToProperty = function(element) {
		element.setAttribute('data-in2studyfinder-is-selected', 'true');
		element.style.fontWeight = 'bold';
		ExportModule.selectedPropertiesCount++;
	};

	/**
	 * @param element
	 */
	ExportModule.removeSelectionFromProperty = function(element) {
		element.setAttribute('data-in2studyfinder-is-selected', 'false');
		element.style.fontWeight = 'normal';
		ExportModule.selectedPropertiesCount--;
	};

	/**
	 *
	 */
	ExportModule.updateSelectedPropertiesCount = function() {
		var element = document.querySelector('.js-in2studyfinder-selected-fields-count');
		element.innerHTML = ExportModule.selectedPropertiesCount;
	};

	/**
	 * @param element
	 * @return void
	 */
	ExportModule.hideElement = function(element) {
		element.style.display = 'none';
	};

	/**
	 * @param element
	 * @return void
	 */
	ExportModule.showElement = function(element) {
		element.style.display = 'inline-block';
	};

	ExportModule.initialize();
});
