import {UrlUtility} from "../utility/urlUtility";
import {LoaderUtility} from "../utility/loaderUtility";
import {ArrayUtility} from "../utility/arrayUtility";

class Filter {

  constructor(pluginContainer) {
    this.identifier = {
      container: '.in2studyfinder',
      filterContainer: '.js-in2studyfinder-filter',
      showFilterButton: '.js-in2studyfinder-filter-button-show',
      hideFilterButton: '.js-in2studyfinder-filter-button-reset',
      filterSectionContainer: '.js-in2studyfinder-filter-section-container',
      filterOptionContainer: '.js-in2studyfinder-filter-options',
      filterSection: '.js-in2studyfinder-filter-section',
      filterCheckbox: '.js-in2studyfinder-checkbox',
      filterShowAllCheckbox: '.js-in2studyfinder-checkbox-all',
      filterRadio: '.js-in2studyfinder-radio',
      filterLegend: '.js-in2studyfinder-filter-legend',
      hideElement: '.u-in2studyfinder-hide',

    }
    this.filter = [];
    this.studyfinderElement = pluginContainer;
    this.filterElement = pluginContainer.querySelector(this.identifier.filterContainer);
    this.openFilterOnLoad = true;
  }

  init() {
    this.setEventListener();
    this.prepareFilter();

    let hashArguments = UrlUtility.getHashArgumentsFromUrl();

    if (hashArguments.length > 0) {
      this.updateFilterByHashArguments(hashArguments);
    }
  }

  update(studyfinderElement) {
    this.filterElement = studyfinderElement.querySelector(this.identifier.filterContainer);
    this.setEventListener();
    this.prepareFilter();
  }

  setEventListener() {
    // hide filter button
    this.filterElement.querySelector(this.identifier.hideFilterButton).addEventListener('click', function(event) {
      if (this.filter.length === 0) {
        this.toggleFilterVisibility();
      } else {
        this.resetAllFilter(event);
        this.call();
      }
    }.bind(this));

    // show filter button
    this.filterElement.querySelector(this.identifier.showFilterButton).addEventListener('click', function(event) {
      this.toggleFilterVisibility(event);
    }.bind(this));


    this.filterElement.querySelectorAll(this.identifier.filterSection).forEach(function(fieldSet) {
      // visibility toggle of filter sections
      fieldSet.querySelector(this.identifier.filterLegend).addEventListener('click', function(event) {
        event.target.nextElementSibling.classList.toggle(this.identifier.hideElement.substring(1));
      }.bind(this));

      // tab navigation for filter
      fieldSet.addEventListener('keypress', function(event) {
        if (event.which === 13) {
          event.target.querySelector(this.identifier.filterLegend).click();
        }
      }.bind(this));
    }.bind(this));

    // set eventListener for filter checkboxes
    this.setFilterCheckboxEventListener();
  }

  prepareFilter() {
    this.prepareCheckboxes();

    // open selected filter sections
    if (this.filter.length > 0 && this.openFilterOnLoad) {
      this.toggleFilterVisibility();

      this.filter.forEach(function(filterName) {
        let filterFieldset = this.filterElement.querySelector('[data-filtergroup="' + filterName + '"]');
        let filter = filterFieldset.querySelector(this.identifier.filterOptionContainer);

        filter.classList.toggle(this.identifier.hideElement.substring(1));
      }.bind(this));
    }
  }

  prepareCheckboxes() {
    this.filterElement.querySelectorAll(this.identifier.filterOptionContainer).forEach(function(filterOptionContainer) {
      let filterStatus = this.isFilterSet(filterOptionContainer);

      if (filterStatus) {
        if (this.filter.indexOf(filterOptionContainer.closest('[data-filtergroup]').getAttribute('data-filtergroup')) === -1) {
          this.filter.push(filterOptionContainer.closest('[data-filtergroup]').getAttribute('data-filtergroup'));
        }

        let showAllCheckbox = filterOptionContainer.querySelector(this.identifier.filterShowAllCheckbox);

        showAllCheckbox.checked = false;
        showAllCheckbox.disabled = false;
      } else {
        let index = this.filter.indexOf(filterOptionContainer.closest('[data-filtergroup]').getAttribute('data-filtergroup'));
        if (index !== -1) {
          this.filter.splice(index, 1);
        }
      }

    }.bind(this));
  }

  call(paginationPage) {
    let pid = this.filterElement.querySelector('input[name="tx_in2studyfinder_pi1[pluginInformation][pid]"]').value;
    let language = this.filterElement.querySelector('input[name="tx_in2studyfinder_pi1[pluginInformation][languageUid]"]').value;
    let paginationArgument = '';
    let instanceId = this.studyfinderElement.getAttribute('data-in2studyfinder-instance-id')

    if (typeof paginationPage === 'undefined') {
      paginationPage = 1;
    }

    if (typeof paginationPage !== 'undefined') {
      paginationArgument = '&tx_in2studyfinder_pi1[studyCoursesForPage][currentPage]=' + paginationPage;
    }

    LoaderUtility.enableLoader();

    fetch('/index.php?id=' + pid + '&L=' + language + '&type=1308171055' + paginationArgument, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: UrlUtility.serialize(this.filterElement)
    }).then((response) => {
      return response.text();
    }).then((html) => {
      this.setSelectedFilterToUrl(paginationPage);

      let tempElement = document.createElement('div');
      tempElement.innerHTML = html;

      this.studyfinderElement.innerHTML = tempElement.querySelector(this.identifier.container).innerHTML;

      LoaderUtility.disableLoader();
      window.in2studyfinder.getInstance(instanceId).update(this.studyfinderElement);
    });
  }

  setSelectedFilterToUrl(paginationPage) {
    let selectionString = '';
    let filterFieldSets = this.filterElement.querySelectorAll(this.identifier.filterSection);

    filterFieldSets.forEach(function(filterFieldSet) {
      let selectedOptions = filterFieldSet.querySelectorAll(this.identifier.filterCheckbox + ':checked, ' + this.identifier.filterRadio + ':checked');

      if (selectedOptions.length > 0) {
        selectionString += filterFieldSet.getAttribute('data-filtergroup') + '=';

        for (let j = 0; j < selectedOptions.length; j++) {
          selectionString += selectedOptions[j].value + '+';
        }

        if (selectedOptions.length >= 1) {
          selectionString = selectionString.substring(0, selectionString.length - 1);
        }

        selectionString += '&';
      }
    }.bind(this));

    selectionString = selectionString.slice(0, -1);

    if (paginationPage && parseInt(paginationPage) > 1) {
      selectionString += 'page=' + paginationPage;
    }

    if (selectionString !== '') {
      selectionString = '#' + selectionString;
    }

    window.location = location.protocol + '//' + location.host + location.pathname + (location.search ? location.search : '') + selectionString;
  };

  updateFilterByHashArguments(hashArguments) {
    let page = 1;

    hashArguments.forEach(function(hashArgument) {
      // if argument page is set
      if (hashArgument.name === 'page') {
        page = hashArgument.values[0];
      } else {
        if (this.filterElement.querySelector('[data-filtergroup="' + hashArgument.name + '"]') !== null) {
          // set the selected filters
          let filterFieldset = this.filterElement.querySelector('[data-filtergroup="' + hashArgument.name + '"]');
          let checkboxes = filterFieldset.querySelectorAll('input[type=checkbox],input[type=radio]');
          let status = false;

          for (let j = 0; j < checkboxes.length; j++) {
            if (ArrayUtility.isInArray(checkboxes[j].value, hashArgument.values)) {
              checkboxes[j].checked = true;
              status = true;
            }
          }

          if (status) {
            filterFieldset.querySelector(this.identifier.filterShowAllCheckbox).checked = false;
          }
        }
      }
    }.bind(this));

    this.call(page);
  };

  setFilterCheckboxEventListener() {
    this.filterElement.querySelector(this.identifier.filterSectionContainer).addEventListener('click', function(evt) {
        let target = evt.target;

        if (target.tagName === 'INPUT') {
          // if a show all checkbox is clicked
          if (target.classList.contains(this.identifier.filterShowAllCheckbox.substring(1))) {
            let filterContainer = target.closest(this.identifier.filterOptionContainer);
            this.resetFilter(filterContainer);
          }

          // if a specific filter checkbox is clicked
          if (target.classList.contains(this.identifier.filterCheckbox.substring(1)) || target.classList.contains(this.identifier.filterRadio.substring(1))) {
            let showAllCheckbox = target.closest(this.identifier.filterOptionContainer).querySelector(this.identifier.filterShowAllCheckbox);
            showAllCheckbox.checked = false;
            showAllCheckbox.disabled = false;
          }

          this.call();
        }
      }.bind(this)
    );
  }

  toggleFilterVisibility() {
    // toggle fieldset Visibility
    let filterFieldSets = this.filterElement.querySelectorAll(this.identifier.filterSection);

    for (let i = 0; i < filterFieldSets.length; i++) {
      filterFieldSets[i].classList.toggle(this.identifier.hideElement.substring(1));
    }

    // toggle button Visibility
    this.filterElement.querySelector(this.identifier.showFilterButton).classList.toggle(this.identifier.hideElement.substring(1));
    this.filterElement.querySelector(this.identifier.hideFilterButton).classList.toggle(this.identifier.hideElement.substring(1));
  }

  resetAllFilter() {
    let filterSection = this.filterElement.querySelectorAll(this.identifier.filterSection);

    for (let i = 0; i < filterSection.length; i++) {
      this.resetFilter(filterSection[i]);
    }
  }

  resetFilter(filterSection) {
    let showAllCheckbox = filterSection.querySelector(this.identifier.filterShowAllCheckbox);
    let checkboxes = filterSection.querySelectorAll(this.identifier.filterCheckbox);

    showAllCheckbox.checked = true;
    showAllCheckbox.disabled = true;

    for (let i = 0; i < checkboxes.length; i++) {
      checkboxes[i].checked = false;
    }

    let index = this.filter.indexOf(filterSection.getAttribute('data-filtergroup'));

    if (index !== -1) {
      this.filter.splice(index, 1);
    }
  }

  /**
   * checks if a given filter element is set
   *
   * @param filterOptionContainer
   * @returns {boolean}
   */
  isFilterSet(filterOptionContainer) {
    let status = false;

    let filterCheckboxes = filterOptionContainer.querySelectorAll(this.identifier.filterCheckbox + ', ' + this.identifier.filterRadio);

    for (let i = 0; i < filterCheckboxes.length; i++) {
      if (filterCheckboxes[i].checked) {
        status = true;
      }
    }

    return status;
  }
}

export {Filter}
