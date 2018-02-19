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
     * @param event
     */
    ExportModule.exportCourses = function(event) {
		var coursesForExport = SelectCoursesForExportService.getCourseList();
		var selectedCoursesElement = document.querySelector('.in2studyfinder-selected-courses');

		ExportModule.addSelectionToSelectedPropertiesList();

        selectedCoursesElement.value = JSON.stringify(Object.assign({}, coursesForExport));
	};

    /**
	 * selects all items from the selected items select box
     */
    ExportModule.addSelectionToSelectedPropertiesList = function () {
        var selectedCoursesList = document.querySelector('.js-in2studyfinder-selected-properties-list');

        // set all elements to selected
        for (var i = 0; i < selectedCoursesList.options.length; i++) {
            selectedCoursesList.options[i].selected = true;
        }
    };

	ExportModule.initialize();
	return ExportModule;
});
