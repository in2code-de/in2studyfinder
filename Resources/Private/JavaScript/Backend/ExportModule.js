define(['TYPO3/CMS/In2studyfinder/Service/Backend/SelectExportPropertiesService', 'TYPO3/CMS/In2studyfinder/Service/Backend/SelectCoursesForExportService'], function(SelectExportPropertiesService, SelectCoursesForExportService) {
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
		var coursesForExport = SelectCoursesForExportService.getCourseList();
		var selectedCoursesElement = document.querySelector('.in2studyfinder-selected-courses');

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
