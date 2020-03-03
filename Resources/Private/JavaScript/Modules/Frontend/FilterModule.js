define(['TYPO3/CMS/In2studyfinder/Utility/MiscUtility', 'TYPO3/CMS/In2studyfinder/Utility/ArrayUtility', 'TYPO3/CMS/In2studyfinder/Utility/UiUtility', 'TYPO3/CMS/In2studyfinder/Utility/UrlUtility', 'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility'], function(MiscUtility, ArrayUtility, UiUtility, UrlUtility, AjaxUtility) {
  'use strict';

  var FilterModule = {
    identifiers: {
      in2studyfinderContainer: '.in2studyfinder',
      filterForm: '.js-in2studyfinder-filter',
      filterContainer: '.js-in2studyfinder-filter-options',
      filterFieldset: '.js-in2studyfinder-filter-section',
      filterLegend: '.js-in2studyfinder-filter-legend',
      filterCheckbox: '.in2studyfinder-js-checkbox',
      filterRadio: '.in2studyfinder-js-radio',
      filterCheckboxAll: '.in2studyfinder-js-checkbox-all',
      showFilterButton: '.js-in2studyfinder-filter-button-show',
      hideFilterButton: '.js-in2studyfinder-filter-button-reset',
      hideElement: '.u-in2studyfinder-hide',
      isHidden: '.is-hidden'
    },
    filter: []
  };

  /**
   * initialize function
   *
   * @return {void}
   */
  FilterModule.initialize = function() {
    if (document.querySelector(FilterModule.identifiers.filterForm)) {
      FilterModule.setEventListener();
      FilterModule.prepareFilter();
    }
  };

  /**
   *
   */
  FilterModule.prepareFilter = function() {
    FilterModule.prepareCheckboxes();

    // trigger filter update by hash arguments
    var hashArguments = UrlUtility.getHashArgumentsFromUrl();
    if (hashArguments.length > 0 && document.querySelector('[data-in2studyfinder-isAjax="1"]') === null) {
      FilterModule.updateFilterByHashArguments(hashArguments);
    }

    // open selected filter sections
    if (FilterModule.filter.length > 0) {
      FilterModule.toggleFilterVisibility();

      for (var i = 0; i < FilterModule.filter.length; i++) {
        var filterFieldset = document.querySelector('[data-filtergroup="' + FilterModule.filter[i] + '"]');
        var filter = filterFieldset.querySelector(FilterModule.identifiers.filterContainer);

        UiUtility.toggleClassForElement(filter, FilterModule.identifiers.isHidden.substr(1));
      }
    }
  };

  /**
   * removes checked value from checkboxes where not needed
   */
  FilterModule.prepareCheckboxes = function() {
    var filterContainer = document.querySelectorAll(FilterModule.identifiers.filterContainer);

    for (var i = 0; i < filterContainer.length; i++) {
      var filterStatus = FilterModule.isFilterSet(filterContainer[i]);

      if (filterStatus) {
        if (FilterModule.filter.indexOf(filterContainer[i].parentNode.getAttribute('data-filtergroup')) === -1) {
          FilterModule.filter.push(filterContainer[i].parentNode.getAttribute('data-filtergroup'));
        }

        var showAllCheckbox = filterContainer[i].querySelector(FilterModule.identifiers.filterCheckboxAll);
        showAllCheckbox.checked = false;
        showAllCheckbox.disabled = false;
      }
    }
  };

  /**
   *
   * @param hashArguments
   */
  FilterModule.updateFilterByHashArguments = function(hashArguments) {
    for (var i = 0; i < hashArguments.length; i++) {
      var page = 1;
      // if argument page is set
      if (hashArguments[i].name === 'page') {
        page = hashArguments[i].values[0];
      } else {
        if (document.querySelector('[data-filtergroup="' + hashArguments[i].name + '"]') !== null) {
          // set the selected filters
          var filterFieldset = document.querySelector('[data-filtergroup="' + hashArguments[i].name + '"]');
          var checkboxes = filterFieldset.querySelectorAll('input[type=checkbox]');
          var status = false;

          for (var j = 0; j < checkboxes.length; j++) {
            if (ArrayUtility.isInArray(checkboxes[j].value, hashArguments[i].values)) {
              checkboxes[j].checked = true;
              status = true;
            }
          }

          if (status) {
            filterFieldset.querySelector(FilterModule.identifiers.filterCheckboxAll).checked = false;
          }
        }
      }
    }

    FilterModule.updateFilter(page);
  };


  /**
   * sets event listeners
   */
  FilterModule.setEventListener = function() {
    // hide filter button
    var hideFilter = document.querySelector(FilterModule.identifiers.hideFilterButton);
    hideFilter.addEventListener('click', FilterModule.resetAllFilter);

    // show filter button
    var showFilter = document.querySelector(FilterModule.identifiers.showFilterButton);
    showFilter.addEventListener('click', FilterModule.toggleFilterVisibility);

    // toggle filter section visibility
    FilterModule.setFilterVisibilityEventListener();

    // set eventListener for filter checkboxes
    FilterModule.setFilterCheckboxEventListener();
  };

  /**
   * add checkbox event listener
   */
  FilterModule.setFilterCheckboxEventListener = function() {
    document.querySelector('.c-in2studyfinder-filter__sections').addEventListener('click', function(evt) {
        var target = evt.target;

        if (target.tagName === 'INPUT') {
          // if an show all checkbox is clicked
          if (target.classList.contains(FilterModule.identifiers.filterCheckboxAll.substr(1))) {
            var filterContainer = target.parentNode;
            FilterModule.resetFilter(filterContainer);
          }

          // if an specific filter checkbox is clicked
          if (
            target.classList.contains(FilterModule.identifiers.filterCheckbox.substr(1)) || target.classList.contains(FilterModule.identifiers.filterRadio.substr(1))
          ) {
            var showAllCheckbox = target.parentNode.querySelector(FilterModule.identifiers.filterCheckboxAll);
            showAllCheckbox.checked = false;
            showAllCheckbox.disabled = false;
          }

          FilterModule.updateFilter();
        }
      }
    );
  };

  /**
   * reset all filter
   */
  FilterModule.resetAllFilter = function() {

    if (FilterModule.filter.length === 0) {
      FilterModule.toggleFilterVisibility();
    } else {
      var filterContainers = document.querySelectorAll(FilterModule.identifiers.filterContainer);
      FilterModule.toggleFilterVisibility();

      for (var i = 0; i < filterContainers.length; i++) {
        FilterModule.resetFilter(filterContainers[i]);
      }

      FilterModule.updateFilter();
    }
  };

  /**
   * resets an given filter
   *
   * @param filterContainer
   */
  FilterModule.resetFilter = function(filterContainer) {
    var showAllCheckbox = filterContainer.querySelector(FilterModule.identifiers.filterCheckboxAll);
    var filterCheckboxes = filterContainer.querySelectorAll(FilterModule.identifiers.filterCheckbox);

    showAllCheckbox.checked = true;
    showAllCheckbox.disabled = true;

    for (var i = 0; i < filterCheckboxes.length; i++) {
      filterCheckboxes[i].checked = false;
    }

    var index = FilterModule.filter.indexOf(filterContainer.parentNode.getAttribute('data-filtergroup'));
    if (index !== -1) {
      FilterModule.filter.splice(index, 1);
    }
  };

  /**
   * main function
   *
   * update the filtering
   */
  FilterModule.updateFilter = function(paginationPage) {
    var in2studyfinderContainer = document.querySelector(FilterModule.identifiers.in2studyfinderContainer);
    var filterForm = document.querySelector(FilterModule.identifiers.filterForm);
    var pluginUid = in2studyfinderContainer.getAttribute('data-plugin-uid');
    var pid = in2studyfinderContainer.getAttribute('data-pid');
    var sysLanguageUid = in2studyfinderContainer.getAttribute('data-in2studyfinder-language');
    var pluginUidArgument = '', languageArgument = '', paginationArgument = '', url = '';

    if (typeof paginationPage === 'undefined') {
      paginationPage = 1;
    }

    if (typeof pluginUid !== 'undefined') {
      pluginUidArgument = '&ce=' + pluginUid;
    }

    if (typeof sysLanguageUid !== 'undefined' && sysLanguageUid !== null) {
      languageArgument = '&L=' + sysLanguageUid;
    }

    if (typeof paginationPage !== 'undefined') {
      paginationArgument = '&tx_in2studyfinder_pi1[@widget_0][currentPage]=' + paginationPage;
    }

    if (typeof pid !== 'undefined' && pid !== null) {
      url = '/index.php?id=' + pid + '&type=1308171055&studyFinderAjaxRequest=1' + pluginUidArgument + languageArgument + paginationArgument;
    } else {
      url = '/?type=1308171055&studyFinderAjaxRequest=1' + pluginUidArgument + languageArgument + paginationArgument;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState === 1) {
        UiUtility.enableLoader();
      }

      if (this.readyState === 4 && this.status === 200) {
        FilterModule.setSelectedFilterToUrl(paginationPage);

        var tempElement = document.createElement('div');
        tempElement.innerHTML = xhttp.responseText;

        document.querySelector(FilterModule.identifiers.in2studyfinderContainer).parentNode.replaceChild(
          tempElement.querySelector(FilterModule.identifiers.in2studyfinderContainer),
          document.querySelector(FilterModule.identifiers.in2studyfinderContainer)
        );

        var Frontend = require("TYPO3/CMS/In2studyfinder/Frontend");
        Frontend.initialize();
        UiUtility.disableLoader();
      }
    };

    xhttp.open('POST', url, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(UrlUtility.serialize(filterForm));
  };

  /**
   * Save Selected Options to Url
   */
  FilterModule.setSelectedFilterToUrl = function(paginationPage) {
    var selectionString = '';
    var form = document.querySelector(FilterModule.identifiers.filterForm);
    var fieldsetNodeList = form.querySelectorAll(FilterModule.identifiers.filterFieldset);

    for (var i = 0; i < fieldsetNodeList.length; i++) {
      var selectedOptions = fieldsetNodeList[i].querySelectorAll(FilterModule.identifiers.filterCheckbox +
        ':checked, ' + FilterModule.identifiers.filterRadio + ':checked');

      if (selectedOptions.length > 0) {
        selectionString += fieldsetNodeList[i].getAttribute('data-filtergroup') + '=';

        for (var j = 0; j < selectedOptions.length; j++) {
          selectionString += selectedOptions[j].value + '+';
        }

        if (selectedOptions.length >= 1) {
          selectionString = selectionString.substring(0, selectionString.length - 1);
        }

        selectionString += '&';
      }
    }

    if (paginationPage) {
      selectionString += 'page=' + paginationPage;
    }

    window.location = location.protocol + '//' + location.host + location.pathname + (location.search ? location.search : '') + '#' + selectionString;
  };

  /**
   * initialize the eventListener for the filter sections.
   *
   *
   * WORKAROUND:
   * At this point we only refactor the javascript.
   * We do not add breaking changes at this point.
   *
   * We do this with an forEach because the parent container
   * has only an style class yet. Later an js class will be added
   * to the container.
   */
  FilterModule.setFilterVisibilityEventListener = function() {

    var fieldsets = document.querySelectorAll(FilterModule.identifiers.filterFieldset);

    for (var i = 0; i < fieldsets.length; i++) {
      fieldsets[i].querySelector(FilterModule.identifiers.filterLegend).addEventListener('click', function() {
        var targetFilter = this.parentNode;
        var filter = targetFilter.querySelector(FilterModule.identifiers.filterContainer);

        UiUtility.toggleClassForElement(filter, FilterModule.identifiers.isHidden.substr(1));
      });
    }
  };

  /**
   * Toggles the filter fieldset visibility
   */
  FilterModule.toggleFilterVisibility = function() {

    // toggle fieldset Visibility
    var filterFieldSets = document.querySelectorAll(FilterModule.identifiers.filterFieldset);

    for (var i = 0; i < filterFieldSets.length; i++) {
      UiUtility.toggleClassForElement(filterFieldSets[i], FilterModule.identifiers.hideElement.substr(1));

    }

    // toggle button Visibility
    var showFilterButton = document.querySelector(FilterModule.identifiers.showFilterButton);
    var hideFilterButton = document.querySelector(FilterModule.identifiers.hideFilterButton);
    UiUtility.toggleClassForElement(showFilterButton, FilterModule.identifiers.hideElement.substr(1));
    UiUtility.toggleClassForElement(hideFilterButton, FilterModule.identifiers.hideElement.substr(1));
  };

  /**
   * checks if an given filter element is set
   *
   * @param filterContainer
   * @returns {boolean}
   */
  FilterModule.isFilterSet = function(filterContainer) {
    var status = false;

    var filterCheckboxes = filterContainer.querySelectorAll(FilterModule.identifiers.filterCheckbox + ', ' +
      FilterModule.identifiers.filterRadio);

    for (var i = 0; i < filterCheckboxes.length; i++) {
      if (filterCheckboxes[i].checked) {
        status = true;
      }
    }

    return status;
  };

  return FilterModule;
});
