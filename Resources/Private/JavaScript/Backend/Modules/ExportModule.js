define(['TYPO3/CMS/In2studyfinder/Backend/Modules/Export/SelectCoursesModule', 'TYPO3/CMS/In2studyfinder/Backend/Modules/Export/SelectPropertiesModule'], function(SelectCoursesModule, SelectPropertiesModule) {
	'use strict';

	var ExportModule = {
		selectedPropertiesCount: 0,
		propertyList: []
	};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	ExportModule.initialize = function() {
		SelectCoursesModule.initialize();
		SelectPropertiesModule.initialize();
		ExportModule.addEventListener();
	};

	/**
	 * Initialize event listener
	 */
	ExportModule.addEventListener = function() {

		// export button
		var exportButton = document.querySelector('.js-in2studyfinder-export-courses');
		exportButton.addEventListener('click', ExportModule.exportCourses);

	};

	/**
	 * export courses
	 */
	ExportModule.exportCourses = function() {
		ExportModule.addSelectionToSelectedPropertiesList();
	};

	/**
	 * selects all items from the selected items select box
	 */
	ExportModule.addSelectionToSelectedPropertiesList = function() {
		var selectedCoursesList = document.querySelector('.js-in2studyfinder-selected-properties-list');

		// set all elements to selected
		for (var i = 0; i < selectedCoursesList.options.length; i++) {
			selectedCoursesList.options[i].selected = true;
		}
	};

	ExportModule.initialize();
	return ExportModule;
});
