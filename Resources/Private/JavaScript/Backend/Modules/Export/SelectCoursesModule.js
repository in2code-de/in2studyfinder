define(['TYPO3/CMS/In2studyfinder/Utility/UiUtility', 'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility', 'TYPO3/CMS/In2studyfinder/Utility/UrlUtility'], function(UiUtility, AjaxUtility, UrlUtility) {
  'use strict';

  var SelectCoursesModule = {
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
  SelectCoursesModule.initialize = function() {
    SelectCoursesModule.preparePagination();
    SelectCoursesModule.prepareSelectedCourses();
    SelectCoursesModule.addEventListener();
  };

  /**
   * initializes all event listeners
   */
  SelectCoursesModule.addEventListener = function() {

    // event listener for select all courses checkbox
    var selectAllCheckbox = document.querySelector(SelectCoursesModule.identifiers.checkAllCheckbox);
    selectAllCheckbox.addEventListener('click', SelectCoursesModule.toggleAllCoursesSelect);

    // event listener for pagination
    var paginationList = document.querySelector(SelectCoursesModule.identifiers.paginationContainer);
    paginationList.addEventListener('click', SelectCoursesModule.callPagination);

    // event listener for update the items per page
    var itemsPerPageSelect = document.querySelector(SelectCoursesModule.identifiers.itemsPerPageSelect);
    itemsPerPageSelect.onchange = function(event) {
      SelectCoursesModule.updateItemsPerPage(event);
    };

    // event listener for update the record language
    var recordLanguageSelect = document.querySelector(SelectCoursesModule.identifiers.changeLanguageSelect);
    recordLanguageSelect.onchange = function(event) {
      SelectCoursesModule.updateRecordLanguage(event);
    };

    // event listener for the selection of courses
    var propertyList = document.querySelector(SelectCoursesModule.identifiers.courseListTableBody);
    propertyList.addEventListener('click', SelectCoursesModule.toggleCourseSelection);
  };

  /**
   * toggles the selection of all courses
   * @param event
   */
  SelectCoursesModule.toggleAllCoursesSelect = function(event) {
    var checkboxes = document.querySelectorAll('.js-in2studyfinder-select-course');
    var selectAllCheckbox = event.target;
    var status;

    if (selectAllCheckbox.checked) {
      status = 1;
    } else {
      status = 0;
    }

    for (var i = 0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      var courseUid = checkbox.getAttribute('data-in2studyfinder-course-uid');
      if (status === 0 && checkbox.checked) {
        if (courseUid) {
          SelectCoursesModule.removeCourseFromList(courseUid);
        }
      }

      if (status === 1 && !checkbox.checked) {
        if (courseUid) {
          SelectCoursesModule.addCourseToList(courseUid);
        }
      }
      checkbox.checked = status;
    }

    SelectCoursesModule.updateSelectedCoursesCount();
  };

  /**
   * Set the Checkboxes checked for the available courses in the courseList
   */
  SelectCoursesModule.prepareSelectedCourses = function() {
    var courseList = SelectCoursesModule.coursesList;

    if (courseList.length > 0) {
      // if courseList !== empty
      // set every existing course from this page in the courseList to active
      for (var i = 0; i < SelectCoursesModule.coursesList.length; i++) {
        var checkbox = document.querySelector('#course-' + SelectCoursesModule.coursesList[i]);
        if (checkbox !== null) {
          checkbox.checked = 1;
        }
      }
    }
  };

  /**
   * updates the results for the shown courses
   * @param event
   */
  SelectCoursesModule.updateItemsPerPage = function(event) {
    var selectedOptions = event.target.selectedOptions;
    var url = selectedOptions[0].getAttribute('data-action');
    if (typeof url !== 'undefined') {
      SelectCoursesModule.paginationAjaxCall(url);
    }
  };

  /**
   * updates the results for the shown courses
   * @param event
   */
  SelectCoursesModule.updateRecordLanguage = function(event) {
    var selectedOptions = event.target.selectedOptions;
    var url = selectedOptions[0].getAttribute('data-action');

    if (typeof url !== 'undefined') {
      if (SelectCoursesModule.resetCourseList()) {
        SelectCoursesModule.paginationAjaxCall(url);
      }
    }
  };

  /**
   *
   * @param event
   *
   * @return {void}
   */
  SelectCoursesModule.callPagination = function(event) {
    event.preventDefault();
    var url = event.target.href;
    var itemsPerPage = document.querySelector('.js-in2studyfinder-itemsPerPage').value;

    if (typeof url !== 'undefined') {
      url = UrlUtility.addAttributeToUrl(
        url,
        'tx_in2studyfinder_web_in2studyfinderm1[@widget_0][itemsPerPage]',
        itemsPerPage
      );
      SelectCoursesModule.paginationAjaxCall(url);
    }
  };

  /**
   *
   * @param url string
   *
   * @return {void}
   */
  SelectCoursesModule.paginationAjaxCall = function(url) {
    AjaxUtility.ajaxCall(
      url,
      SelectCoursesModule.onPaginationCallStart,
      SelectCoursesModule.onPaginationCallSuccess
    );
  };

  /**
   * @return {void}
   */
  SelectCoursesModule.onPaginationCallStart = function() {
    UiUtility.toggleClassForElement(
      document.querySelector(SelectCoursesModule.identifiers.loader),
      SelectCoursesModule.identifiers.loaderActive
    );
  };

  /**
   * @param xhttp
   *
   * @return {void}
   */
  SelectCoursesModule.onPaginationCallSuccess = function(xhttp) {
    var tempElement = document.createElement('div');
    var selectCourseContainerClass = 'js-in2studyfinder-select-course-container';

    tempElement.innerHTML = xhttp.responseText;

    document.querySelector('.' + selectCourseContainerClass).innerHTML =
      tempElement.querySelector('.' + selectCourseContainerClass).innerHTML;

    SelectCoursesModule.initialize();
    SelectCoursesModule.updateSelectedCoursesCount();
    UiUtility.toggleClassForElement(
      document.querySelector(SelectCoursesModule.identifiers.loader),
      SelectCoursesModule.identifiers.loaderActive
    );
  };

  /**
   * @param event
   *
   * @return {void}
   */
  SelectCoursesModule.toggleCourseSelection = function(event) {
    var selectedElement = event.target;

    if (selectedElement.classList.contains('js-in2studyfinder-select-course')) {
      if (selectedElement.checked) {
        SelectCoursesModule.addCourseToList(selectedElement.value);
      } else {
        SelectCoursesModule.removeCourseFromList(selectedElement.value);
      }

      SelectCoursesModule.updateSelectedCoursesCount();
    }

  };

  /**
   * @param courseUid
   *
   * @return void
   */
  SelectCoursesModule.addCourseToList = function(courseUid) {
    SelectCoursesModule.coursesList.push(courseUid);
  };

  /**
   * @param courseUid
   *
   * @return void
   */
  SelectCoursesModule.removeCourseFromList = function(courseUid) {
    SelectCoursesModule.coursesList.pop(courseUid);
  };

  /**
   * updates the selected courses count
   */
  SelectCoursesModule.updateSelectedCoursesCount = function() {
    var element = document.querySelector('.js-in2studyfinder-selected-courses-count');
    element.innerHTML = SelectCoursesModule.coursesList.length;
  };

  /**
   * @returns {Array}
   */
  SelectCoursesModule.getCourseList = function() {
    return SelectCoursesModule.coursesList;
  };

  /**
   * move the pagination to the right spot in the dom
   */
  SelectCoursesModule.preparePagination = function() {
    var pagination = document.querySelector(SelectCoursesModule.identifiers.paginationContainer);
    document.querySelector('.js-in2studyfinder-pagination').appendChild(pagination);
  };

  /**
   * @returns {boolean}
   */
  SelectCoursesModule.resetCourseList = function() {
    var status = true;

    if (SelectCoursesModule.coursesList.length > 0) {
      if (confirm('all currently selected courses will be deselected. Will you proceed?')) {
        SelectCoursesModule.coursesList = [];
        SelectCoursesModule.updateSelectedCoursesCount();
      } else {
        status = false;
      }
    }

    return status;
  };

  return SelectCoursesModule;
});
