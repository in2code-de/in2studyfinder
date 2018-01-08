define([], function() {
	'use strict';

	var SelectPropertiesForExportService = {
		selectedPropertiesCount: 0,
		propertyList: []
	};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	SelectPropertiesForExportService.initialize = function() {
		SelectPropertiesForExportService.addEventListenerToPropertyList();
	};

	/**
	 *
	 */
	SelectPropertiesForExportService.addEventListenerToPropertyList = function() {
		var propertyList = document.querySelector('.js-in2studyfinder-property-list');
		propertyList.addEventListener('click', SelectPropertiesForExportService.togglePropertySelection);
	};

	/**
	 * @param event
	 */
	SelectPropertiesForExportService.togglePropertySelection = function(event) {
		var selectedElement = event.target;
		if (selectedElement.getAttribute('data-in2studyfinder-is-selectable') === 'true') {
			if (selectedElement.getAttribute('data-in2studyfinder-is-selected') !== 'true') {
				SelectPropertiesForExportService.addSelectionToProperty(selectedElement);
				SelectPropertiesForExportService.addPropertyToList(selectedElement.getAttribute('data-in2studyfinder-property-path'));
			} else {
				SelectPropertiesForExportService.removeSelectionFromProperty(selectedElement);
				SelectPropertiesForExportService.removePropertyFromList(selectedElement.getAttribute('data-in2studyfinder-property-path'));
			}
		}

		SelectPropertiesForExportService.updateSelectedPropertiesCount();
	};

	/**
	 * @param element
	 */
	SelectPropertiesForExportService.addSelectionToProperty = function(element) {
		element.setAttribute('data-in2studyfinder-is-selected', 'true');
		element.style.fontWeight = 'bold';
		SelectPropertiesForExportService.selectedPropertiesCount++;
	};

	/**
	 * @param element
	 */
	SelectPropertiesForExportService.removeSelectionFromProperty = function(element) {
		element.setAttribute('data-in2studyfinder-is-selected', 'false');
		element.style.fontWeight = 'normal';
		SelectPropertiesForExportService.selectedPropertiesCount--;
	};

	/**
	 *
	 */
	SelectPropertiesForExportService.updateSelectedPropertiesCount = function() {
		var element = document.querySelector('.js-in2studyfinder-selected-properties-count');
		element.innerHTML = SelectPropertiesForExportService.selectedPropertiesCount;
	};

	/**
	 * @param propertyName
	 *
	 * @return void
	 */
	SelectPropertiesForExportService.addPropertyToList = function(propertyName) {
		SelectPropertiesForExportService.propertyList.push(propertyName);
	};

	/**
	 * @param propertyName
	 *
	 * @return void
	 */
	SelectPropertiesForExportService.removePropertyFromList = function(propertyName) {
		SelectPropertiesForExportService.propertyList.pop(propertyName);
	};

	/**
	 * @returns {Array}
	 */
	SelectPropertiesForExportService.getPropertyList = function() {
		return SelectPropertiesForExportService.propertyList;
	};

	SelectPropertiesForExportService.initialize();
	return SelectPropertiesForExportService;
});
