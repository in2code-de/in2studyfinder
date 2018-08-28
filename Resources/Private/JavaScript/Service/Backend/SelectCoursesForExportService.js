define(['TYPO3/CMS/In2studyfinder/Utility/UiUtility', 'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility', 'TYPO3/CMS/In2studyfinder/Utility/UrlUtility'], function(UiUtility, AjaxUtility, UrlUtility) {
	'use strict';

	var SelectCoursesForExportService = {
		coursesList: [],
		identifiers: {
			checkAllCheckbox: '.js-in2studyfinder-check-all',
			paginationContainer: '.js-in2studyfinder-pagebrowser',
			itemsPerPageSelect: '.js-in2studyfinder-itemsPerPage',
			changeLanguageSelect: '.js-in2studyfinder-recordLanguage',
			courseListTableBody: '.js-in2studyfinder-course-list',
			loader: '.in2js-in2studyfinder-loader',
			loaderActive: '.in2js-in2studyfinder-loader--active'
		}
	};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	SelectCoursesForExportService.initialize = function() {
		SelectCoursesForExportService.preparePagination();
		SelectCoursesForExportService.prepareSelectedCourses();
		SelectCoursesForExportService.addEventListener();
	};

	/**
	 * initializes all event listeners
	 */
	SelectCoursesForExportService.addEventListener = function() {

		// event listener for select all courses checkbox
		var selectAllCheckbox = document.querySelector(SelectCoursesForExportService.identifiers.checkAllCheckbox);
		selectAllCheckbox.addEventListener('click', SelectCoursesForExportService.toggleAllCoursesSelect);

		// event listener for pagination
		var paginationList = document.querySelector(SelectCoursesForExportService.identifiers.paginationContainer);
		paginationList.addEventListener('click', SelectCoursesForExportService.callPagination);

		// event listener for update the items per page
		var itemsPerPageSelect = document.querySelector(SelectCoursesForExportService.identifiers.itemsPerPageSelect);
		itemsPerPageSelect.onchange = function(event) {
			SelectCoursesForExportService.updateItemsPerPage(event);
		};

		// event listener for update the record language
		var recordLanguageSelect = document.querySelector(SelectCoursesForExportService.identifiers.changeLanguageSelect);
		recordLanguageSelect.onchange = function(event) {
			SelectCoursesForExportService.updateRecordLanguage(event);
		};

		// event listener for the selection of courses
		var propertyList = document.querySelector(SelectCoursesForExportService.identifiers.courseListTableBody);
		propertyList.addEventListener('click', SelectCoursesForExportService.toggleCourseSelection);
	};

	/**
	 * toggles the selection of all courses
	 * @param event
	 */
	SelectCoursesForExportService.toggleAllCoursesSelect = function(event) {
		var checkboxes = document.querySelectorAll('.js-in2studyfinder-select-course');
		var selectAllCheckbox = event.target;
		var status;

		if (selectAllCheckbox.checked) {
			status = 1;
		} else {
			status = 0;
		}

		checkboxes.forEach(function(checkbox) {
			var courseUid = checkbox.getAttribute('data-in2studyfinder-course-uid');
			if (status === 0 && checkbox.checked) {
				if (courseUid) {
					SelectCoursesForExportService.removeCourseFromList(courseUid);
				}
			}

			if (status === 1 && !checkbox.checked) {
				if (courseUid) {
					SelectCoursesForExportService.addCourseToList(courseUid);
				}
			}
			checkbox.checked = status;
		});

		SelectCoursesForExportService.updateSelectedCoursesCount();
	};

	/**
	 * Set the Checkboxes checked for the available courses in the courseList
	 */
	SelectCoursesForExportService.prepareSelectedCourses = function() {
		var courseList = SelectCoursesForExportService.coursesList;

		if (courseList.length > 0) {
			// if courseList !== empty
			// set every existing course from this page in the courseList to active
			SelectCoursesForExportService.coursesList.forEach(function(data) {
				var checkbox = document.querySelector('#course-' + data);
				if (checkbox !== null) {
					checkbox.checked = 1;
				}
			});
		}
	};

	/**
	 * updates the results for the shown courses
	 * @param event
	 */
	SelectCoursesForExportService.updateItemsPerPage = function(event) {
		var selectedOptions = event.target.selectedOptions;
		var url = selectedOptions[0].getAttribute('data-action');
		if (typeof url !== 'undefined') {
			SelectCoursesForExportService.paginationAjaxCall(url);
		}
	};

	/**
	 * updates the results for the shown courses
	 * @param event
	 */
	SelectCoursesForExportService.updateRecordLanguage = function(event) {
		var selectedOptions = event.target.selectedOptions;
		var url = selectedOptions[0].getAttribute('data-action');

		if (typeof url !== 'undefined') {
			if (SelectCoursesForExportService.resetCourseList()) {
				SelectCoursesForExportService.paginationAjaxCall(url);
			}
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
		AjaxUtility.ajaxCall(
			url,
			SelectCoursesForExportService.onPaginationCallStart,
			SelectCoursesForExportService.onPaginationCallSuccess
		);
	};

	/**
	 * @return {void}
	 */
	SelectCoursesForExportService.onPaginationCallStart = function() {
		UiUtility.toggleClassForElement(
			document.querySelector(SelectCoursesForExportService.identifiers.loader),
			SelectCoursesForExportService.identifiers.loaderActive
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
			document.querySelector(SelectCoursesForExportService.identifiers.loader),
			SelectCoursesForExportService.identifiers.loaderActive
		);
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
			} else {
				SelectCoursesForExportService.removeCourseFromList(selectedElement.value);
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
		SelectCoursesForExportService.coursesList.pop(courseUid);
	};

	/**
	 * updates the selected courses count
	 */
	SelectCoursesForExportService.updateSelectedCoursesCount = function() {
		var element = document.querySelector('.js-in2studyfinder-selected-courses-count');
		element.innerHTML = SelectCoursesForExportService.coursesList.length;
	};

	/**
	 * @returns {Array}
	 */
	SelectCoursesForExportService.getCourseList = function() {
		return SelectCoursesForExportService.coursesList;
	};

	/**
	 * move the pagination to the right spot in the dom
	 */
	SelectCoursesForExportService.preparePagination = function() {
		var pagination = document.querySelector(SelectCoursesForExportService.identifiers.paginationContainer);
		document.querySelector('.js-in2studyfinder-pagination').appendChild(pagination);
	};

	/**
	 * @returns {boolean}
	 */
	SelectCoursesForExportService.resetCourseList = function() {
		var status = true;

		if (SelectCoursesForExportService.coursesList.length > 0) {
			if (confirm('all currently selected courses will be deselected. Will you proceed?')) {
				SelectCoursesForExportService.coursesList = [];
				SelectCoursesForExportService.updateSelectedCoursesCount();
			} else {
				status = false;
			}
		}

		return status;
	};

	SelectCoursesForExportService.initialize();
	return SelectCoursesForExportService;
});
