define(['TYPO3/CMS/In2studyfinder/Utility/UiUtility', 'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility', 'TYPO3/CMS/In2studyfinder/Utility/UrlUtility'], function(UiUtility, AjaxUtility, UrlUtility) {
	'use strict';

	var SelectCoursesForExportService = {
		selectedCoursesCount: 0,
		coursesList: []
	};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	SelectCoursesForExportService.initialize = function() {
		SelectCoursesForExportService.preparePagination();
		SelectCoursesForExportService.prepareSelectedCourses();
		SelectCoursesForExportService.addEventListenerToPropertyList();
		SelectCoursesForExportService.addPaginationEventListener();
		SelectCoursesForExportService.addChangeItemsPerPageEventListener();
	};

    /**
	 * Set the Checkboxes checked for the available courses in the courseList
     */
	SelectCoursesForExportService.prepareSelectedCourses = function() {
		var courseList = SelectCoursesForExportService.coursesList;

		if (courseList.length > 0) {
            console.log(courseList.length);
            // if courseList !== empty
            // set every existing course from this page in the courseList to active
            console.log(SelectCoursesForExportService.coursesList);
            //SelectCoursesForExportService.coursesList.each(console.log('test'));
        }
	};

	/**
	 * @return {void}
	 */
	SelectCoursesForExportService.addPaginationEventListener = function() {
		var paginationList = document.querySelector('.js-in2studyfinder-pagebrowser');
		paginationList.addEventListener('click', SelectCoursesForExportService.callPagination);
	};

	/**
	 * @return {void}
	 */
	SelectCoursesForExportService.addChangeItemsPerPageEventListener = function() {
		var itemsPerPageSelect = document.querySelector('.js-in2studyfinder-itemsPerPage');
		itemsPerPageSelect.onchange = function(event) {
			SelectCoursesForExportService.updateItemsPerPage(event);
		};
	};

	SelectCoursesForExportService.updateItemsPerPage = function(event) {
		var selectedOptions = event.target.selectedOptions;
		var url = selectedOptions[0].getAttribute('data-action');
		if (typeof url !== 'undefined') {
			SelectCoursesForExportService.paginationAjaxCall(url);
		}
	};

	/**
	 *
	 * @param event
	 *
	 * @return {void}
	 */
	SelectCoursesForExportService.callPagination = function(event) {
		event.preventDefault();
		var url = event.target.href;
		var itemsPerPage = document.querySelector('.js-in2studyfinder-itemsPerPage').value;

		if (typeof url !== 'undefined') {
			url = UrlUtility.addAttributeToUrl(
				url,
				'tx_in2studyfinder_web_in2studyfinderm1[@widget_0][itemsPerPage]',
				itemsPerPage
			);
			SelectCoursesForExportService.paginationAjaxCall(url);
		}
	};

	/**
	 *
	 * @param url string
	 *
	 * @return {void}
	 */
	SelectCoursesForExportService.paginationAjaxCall = function(url) {
		AjaxUtility.ajaxCall(url, SelectCoursesForExportService.onPaginationCallStart, SelectCoursesForExportService.onPaginationCallSuccess);
	};

	/**
	 * @return {void}
	 */
	SelectCoursesForExportService.onPaginationCallStart = function() {
		UiUtility.toggleClassForElement(
			document.querySelector('.in2js-in2studyfinder-loader'),
			'in2studyfinder-loader--active'
		);
	};

	/**
	 * @param xhttp
	 *
	 * @return {void}
	 */
	SelectCoursesForExportService.onPaginationCallSuccess = function(xhttp) {
		var tempElement = document.createElement('div');
		var selectCourseContainerClass = 'js-in2studyfinder-select-course-container';

		tempElement.innerHTML = xhttp.responseText;

		document.querySelector('.' + selectCourseContainerClass).innerHTML =
			tempElement.querySelector('.' + selectCourseContainerClass).innerHTML;

		SelectCoursesForExportService.initialize();
		SelectCoursesForExportService.updateSelectedCoursesCount();
		UiUtility.toggleClassForElement(
			document.querySelector('.in2js-in2studyfinder-loader'),
			'in2studyfinder-loader--active'
		);
	};

	/**
	 * @return {void}
	 */
	SelectCoursesForExportService.addEventListenerToPropertyList = function() {
		var propertyList = document.querySelector('.js-in2studyfinder-course-list');
		propertyList.addEventListener('click', SelectCoursesForExportService.toggleCourseSelection);
	};

	/**
	 * @param event
	 *
	 * @return {void}
	 */
	SelectCoursesForExportService.toggleCourseSelection = function(event) {
		var selectedElement = event.target;

		if (selectedElement.classList.contains('js-in2studyfinder-select-course')) {
			if (selectedElement.checked) {
				SelectCoursesForExportService.addCourseToList(selectedElement.value);
				SelectCoursesForExportService.selectedCoursesCount++;
			} else {
				SelectCoursesForExportService.removeCourseFromList(selectedElement.value);
				SelectCoursesForExportService.selectedCoursesCount--;
			}

			SelectCoursesForExportService.updateSelectedCoursesCount();
		}

	};

	/**
	 * @param courseUid
	 *
	 * @return void
	 */
	SelectCoursesForExportService.addCourseToList = function(courseUid) {
		SelectCoursesForExportService.coursesList.push(courseUid);
	};

	/**
	 * @param courseUid
	 *
	 * @return void
	 */
	SelectCoursesForExportService.removeCourseFromList = function(courseUid) {
		SelectCoursesForExportService.coursesList.push(courseUid);
	};

	/**
	 *
	 */
	SelectCoursesForExportService.updateSelectedCoursesCount = function() {
		var element = document.querySelector('.js-in2studyfinder-selected-courses-count');
		element.innerHTML = SelectCoursesForExportService.selectedCoursesCount;
	};

	/**
	 * @returns {Array}
	 */
	SelectCoursesForExportService.getCourseList = function() {
		return SelectCoursesForExportService.coursesList;
	};

	SelectCoursesForExportService.preparePagination = function () {
        var pagination = document.querySelector('.js-in2studyfinder-pagebrowser');
        document.querySelector('.js-in2studyfinder-pagination').appendChild(pagination);
    };

	SelectCoursesForExportService.initialize();
	return SelectCoursesForExportService;
});
