define(['TYPO3/CMS/In2studyfinder/Service/Backend/SelectPropertiesForExportService', 'TYPO3/CMS/In2studyfinder/Service/Backend/SelectCoursesForExportService'], function(SelectPropertiesForExportService, SelectCoursesForExportService) {
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
		ExportModule.addEventListenerStartExport();
	};

	ExportModule.exportCourses = function() {
		var propertiesForExport = SelectPropertiesForExportService.getPropertyList();
		var coursesForExport = SelectCoursesForExportService.getCourseList();
		var selectedPropertiesElement = document.querySelector('.in2studyfinder-selected-properties');
		var selectedCoursesElement = document.querySelector('.in2studyfinder-selected-courses');

        selectedPropertiesElement.value = JSON.stringify(Object.assign({}, propertiesForExport));
        selectedCoursesElement.value = JSON.stringify(Object.assign({}, coursesForExport));
	};

	/**
	 *
	 */
	ExportModule.addEventListenerStartExport = function() {
		var element = document.querySelector('.js-in2studyfinder-export-courses');
		element.addEventListener('click', ExportModule.exportCourses);
	};

	ExportModule.initialize();
	return ExportModule;
});
