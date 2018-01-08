define([], function() {
	'use strict';

	var UiUtility = {};

	/**
	 * toggle class for element
	 */
	UiUtility.toggleClassForElement = function(element, className) {
		if (element.classList.contains(className)) {
			element.classList.remove(className);
		} else {
			element.classList.add(className);
		}
	};

	/**
	 * @param element
	 * @return void
	 */
	UiUtility.hideElement = function(element) {
		element.style.display = 'none';
	};

	/**
	 * @param element
	 * @return void
	 */
	UiUtility.showElement = function(element) {
		element.style.display = 'inline-block';
	};

	return UiUtility;
});
