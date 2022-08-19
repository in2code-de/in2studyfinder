import {UrlUtility, UiUtility, ArrayUtility} from "../Utility";

import {loader} from "../Components/Loader";
import {frontend} from "../Frontend";

class FilterModule {
  constructor() {
    this.identifiers = {
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
    };
    this.filterToggle = true;
    this.filter = [];
  }

  /**
   * initialize function
   * @param {boolean} isInitialRequest
   * @return {void}
   */
  initialize(isInitialRequest ) {
    if (document.querySelector(this.identifiers.filterForm)) {
      if (document.querySelector(this.identifiers.filterForm).hasAttribute('data-disable-filter-toggle')) {
        this.filterToggle = false;
      }
      this.setEventListener();
      this.prepareFilter(isInitialRequest);
    }
  };

  /**
   * @param {boolean} isInitialRequest
   */
  prepareFilter(isInitialRequest) {
    this.prepareCheckboxes();

    // trigger filter update by hash arguments
    var hashArguments = UrlUtility.getHashArgumentsFromUrl();
    if (hashArguments.length > 0 && isInitialRequest) {
      this.updateFilterByHashArguments(hashArguments);
    }

    // open selected filter sections
    if (this.filter.length > 0 && this.filterToggle) {
      this.toggleFilterVisibility();

      for (var i = 0; i < this.filter.length; i++) {
        var filterFieldset = document.querySelector('[data-filtergroup="' + this.filter[i] + '"]');
        var filter = filterFieldset.querySelector(this.identifiers.filterContainer);

        UiUtility.toggleClassForElement(filter, this.identifiers.isHidden.substr(1));
      }
    }
  };

  /**
   * removes checked value from checkboxes where not needed
   */
  prepareCheckboxes() {
    var filterContainer = document.querySelectorAll(this.identifiers.filterContainer);

    for (var i = 0; i < filterContainer.length; i++) {
      var filterStatus = this.isFilterSet(filterContainer[i]);

      if (filterStatus) {
        if (this.filter.indexOf(filterContainer[i].parentNode.getAttribute('data-filtergroup')) === -1) {
          this.filter.push(filterContainer[i].parentNode.getAttribute('data-filtergroup'));
        }

        var showAllCheckbox = filterContainer[i].querySelector(this.identifiers.filterCheckboxAll);
        showAllCheckbox.checked = false;
        showAllCheckbox.disabled = false;
      }
    }
  };

  /**
   *
   * @param hashArguments
   */
  updateFilterByHashArguments(hashArguments) {
    for (var i = 0; i < hashArguments.length; i++) {
      var page = 1;
      // if argument page is set
      if (hashArguments[i].name === 'page') {
        page = hashArguments[i].values[0];
      } else {
        if (document.querySelector('[data-filtergroup="' + hashArguments[i].name + '"]') !== null) {
          // set the selected filters
          var filterFieldset = document.querySelector('[data-filtergroup="' + hashArguments[i].name + '"]');
          var checkboxes = filterFieldset.querySelectorAll('input[type=checkbox],input[type=radio]');
          var status = false;

          for (var j = 0; j < checkboxes.length; j++) {
            if (ArrayUtility.isInArray(checkboxes[j].value, hashArguments[i].values)) {
              checkboxes[j].checked = true;
              status = true;
            }
          }

          if (status) {
            filterFieldset.querySelector(this.identifiers.filterCheckboxAll).checked = false;
          }
        }
      }
    }

    this.updateFilter(page);
  };


  /**
   * sets event listeners
   */
  setEventListener() {
    let filterModule = this;

    // hide filter button
    var hideFilter = document.querySelector(this.identifiers.hideFilterButton);
    hideFilter.addEventListener('click', this.resetAllFilter);

    // show filter button
    var showFilter = document.querySelector(this.identifiers.showFilterButton);
    showFilter.addEventListener('click', this.toggleFilterVisibility);

    // enable tab navigation for filter
    var fieldSets = document.querySelectorAll(this.identifiers.filterFieldset);
    for (var i = 0; i < fieldSets.length; i++) {
      fieldSets[i].addEventListener('keypress', function (event) {
        if (event.which === 13) {
          event.target.querySelector(filterModule.identifiers.filterLegend).click();
        }
      });
    }

    // toggle filter section visibility
    this.setFilterVisibilityEventListener();

    // set eventListener for filter checkboxes
    this.setFilterCheckboxEventListener();
  };

  /**
   * add checkbox event listener
   */
  setFilterCheckboxEventListener() {
    let filterModule = this;

    document.querySelector('.c-in2studyfinder-filter__sections').addEventListener('click', function(evt) {
        var target = evt.target;

        if (target.tagName === 'INPUT') {
          // if an show all checkbox is clicked
          if (target.classList.contains(filterModule.identifiers.filterCheckboxAll.substr(1))) {
            var filterContainer = target.parentNode;
            filterModule.resetFilter(filterContainer);
          }

          // if an specific filter checkbox is clicked
          if (target.classList.contains(filterModule.identifiers.filterCheckbox.substr(1)) || target.classList.contains(filterModule.identifiers.filterRadio.substr(1))) {
            var showAllCheckbox = target.parentNode.querySelector(filterModule.identifiers.filterCheckboxAll);
            showAllCheckbox.checked = false;
            showAllCheckbox.disabled = false;
          }

          filterModule.updateFilter();
        }
      }
    );
  };

  /**
   * reset all filter
   */
  resetAllFilter() {
    if (filterModule.filter.length === 0) {
      filterModule.toggleFilterVisibility();
    } else {
      var filterContainers = document.querySelectorAll(filterModule.identifiers.filterContainer);
      filterModule.toggleFilterVisibility();

      for (var i = 0; i < filterContainers.length; i++) {
        filterModule.resetFilter(filterContainers[i]);
      }

      filterModule.updateFilter();
    }
  };

  /**
   * resets an given filter
   *
   * @param filterContainer
   */
  resetFilter(filterContainer) {
    var showAllCheckbox = filterContainer.querySelector(this.identifiers.filterCheckboxAll);
    var filterCheckboxes = filterContainer.querySelectorAll(this.identifiers.filterCheckbox);

    showAllCheckbox.checked = true;
    showAllCheckbox.disabled = true;

    for (var i = 0; i < filterCheckboxes.length; i++) {
      filterCheckboxes[i].checked = false;
    }

    var index = this.filter.indexOf(filterContainer.parentNode.getAttribute('data-filtergroup'));
    if (index !== -1) {
      this.filter.splice(index, 1);
    }
  };

  /**
   * main function
   *
   * update the filtering
   */
  updateFilter(paginationPage) {
    var filterForm = document.querySelector(this.identifiers.filterForm);
    var pid = filterForm.querySelector('input[name="tx_in2studyfinder_pi1[pluginInformation][pid]"]').value;
    var paginationArgument = '', url = '';

    if (typeof paginationPage === 'undefined') {
      paginationPage = 1;
    }

    if (typeof paginationPage !== 'undefined') {
      paginationArgument = '&tx_in2studyfinder_pi1[studyCoursesForPage][currentPage]=' + paginationPage;
    }

    url = '/index.php?id=' + pid + '&type=1308171055' + paginationArgument;

    var xhttp = new XMLHttpRequest();
    let filterModule = this;

    xhttp.onreadystatechange = function() {
      if (this.readyState === 1) {
        loader.enableLoader();
      }

      if (this.readyState === 4 && this.status === 200) {
        filterModule.setSelectedFilterToUrl(paginationPage);

        var tempElement = document.createElement('div');
        tempElement.innerHTML = xhttp.responseText;

        document.querySelector(filterModule.identifiers.in2studyfinderContainer).parentNode.replaceChild(
          tempElement.querySelector(filterModule.identifiers.in2studyfinderContainer),
          document.querySelector(filterModule.identifiers.in2studyfinderContainer)
        );

        frontend.initialize();
        loader.disableLoader();
      }
    };

    xhttp.open('POST', url, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(UrlUtility.serialize(filterForm));
  };

  /**
   * Save Selected Options to Url
   */
  setSelectedFilterToUrl(paginationPage) {
    var selectionString = '';
    var form = document.querySelector(this.identifiers.filterForm);
    var fieldsetNodeList = form.querySelectorAll(this.identifiers.filterFieldset);

    for (var i = 0; i < fieldsetNodeList.length; i++) {
      var selectedOptions = fieldsetNodeList[i].querySelectorAll(this.identifiers.filterCheckbox + ':checked, ' + this.identifiers.filterRadio + ':checked');

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
  setFilterVisibilityEventListener() {

    var fieldsets = document.querySelectorAll(filterModule.identifiers.filterFieldset);

    for (var i = 0; i < fieldsets.length; i++) {
      fieldsets[i].querySelector(filterModule.identifiers.filterLegend).addEventListener('click', function() {
        var targetFilter = this.parentNode;
        var filter = targetFilter.querySelector(filterModule.identifiers.filterContainer);

        UiUtility.toggleClassForElement(filter, filterModule.identifiers.isHidden.substr(1));
      });
    }
  };

  /**
   * Toggles the filter fieldset visibility
   */
  toggleFilterVisibility() {
    // toggle fieldset Visibility
    var filterFieldSets = document.querySelectorAll(filterModule.identifiers.filterFieldset);

    for (var i = 0; i < filterFieldSets.length; i++) {
      UiUtility.toggleClassForElement(filterFieldSets[i], filterModule.identifiers.hideElement.substr(1));
    }

    // toggle button Visibility
    var showFilterButton = document.querySelector(filterModule.identifiers.showFilterButton);
    var hideFilterButton = document.querySelector(filterModule.identifiers.hideFilterButton);
    UiUtility.toggleClassForElement(showFilterButton, filterModule.identifiers.hideElement.substr(1));
    UiUtility.toggleClassForElement(hideFilterButton, filterModule.identifiers.hideElement.substr(1));
  };

  /**
   * checks if an given filter element is set
   *
   * @param filterContainer
   * @returns {boolean}
   */
  isFilterSet(filterContainer) {
    var status = false;

    var filterCheckboxes = filterContainer.querySelectorAll(this.identifiers.filterCheckbox + ', ' + this.identifiers.filterRadio);

    for (var i = 0; i < filterCheckboxes.length; i++) {
      if (filterCheckboxes[i].checked) {
        status = true;
      }
    }

    return status;
  };
}

export let filterModule = new FilterModule();
