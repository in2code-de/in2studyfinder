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

    // Focus management for accessibility
    this.lastFocusedElement = null;
    this.focusType = null;
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

    // Restore focus after AJAX call
    this.restoreFocus();
  }

  setEventListener() {
    // Hide filter button / reset button
    this.filterElement.querySelector(this.identifier.hideFilterButton).addEventListener('click', function (event) {
      if (this.filter.length === 0) {
        this.toggleFilterVisibility();
      } else {
        // After reset, focus moves to "show filter" button
        this.focusType = 'showButton';
        this.resetAllFilter(event);
        this.call();
      }
    }.bind(this));

    // Show filter button
    this.filterElement.querySelector(this.identifier.showFilterButton).addEventListener('click', function (event) {
      this.toggleFilterVisibility(event);

      // After opening the filter, focus moves to the first category
      setTimeout(() => {
        const firstFilterSection = this.filterElement.querySelector(this.identifier.filterSection + ':not(' + this.identifier.hideElement + ')');
        if (firstFilterSection) {
          firstFilterSection.focus();
        }
      }, 100);
    }.bind(this));

    this.filterElement.querySelectorAll(this.identifier.filterSection).forEach(function (fieldSet) {
      // Visibility toggle of filter sections
      fieldSet.querySelector(this.identifier.filterLegend).addEventListener('click', function (event) {
        event.target.nextElementSibling.classList.toggle(this.identifier.hideElement.substring(1));
      }.bind(this));

      // Tab navigation for filter
      fieldSet.addEventListener('keypress', function (event) {
        if (event.which === 13) {
          event.target.querySelector(this.identifier.filterLegend).click();
        }
      }.bind(this));
    }.bind(this));

    // Set eventListener for filter checkboxes
    this.setFilterCheckboxEventListener();

    // Handle keyboard navigation for radio buttons and checkboxes
    this.filterElement.addEventListener('keydown', function (keyboardEvent) {
      const target = keyboardEvent.target;
      if (target.type === 'radio' || target.type === 'checkbox') {
        const radios = Array.from(this.filterElement.querySelectorAll(`input[name="${target.name}"]`));
        const currentIndex = radios.indexOf(target);

        // Navigation to previous item with arrows left or up
        if (['ArrowLeft', 'ArrowUp'].includes(keyboardEvent.code)) {
          keyboardEvent.preventDefault();
          const previous = radios[currentIndex - 1] || radios[radios.length - 1];
          previous.focus();
        }
        // Navigation to next item with arrows right or down
        if (['ArrowRight', 'ArrowDown'].includes(keyboardEvent.code)) {
          keyboardEvent.preventDefault();
          const next = radios[currentIndex + 1] || radios[0];
          next.focus();
        }
        // Select with space key
        if (['Space', 'Spacebar'].includes(keyboardEvent.code)) {
          keyboardEvent.preventDefault();
          target.checked = true;

          // Focus handling for "all" filter button
          if (target.classList.contains(this.identifier.filterShowAllCheckbox.substring(1))) {
            this.onClick(keyboardEvent);
            const filterContainer = target.closest(this.identifier.filterOptionContainer);
            this.resetFilter(filterContainer);

            // Check if other filters are still active
            if (this.filter.length === 0) {
              // No filters left: close mask and focus moves to "show filter" button
              this.toggleFilterVisibility();
              setTimeout(() => {
                this.filterElement.querySelector(this.identifier.showFilterButton).focus();
              }, 50);
            } else {
              // Other filters still active: focus moves to parent fieldset
              this.focusType = 'fieldset';
              this.lastFocusedElement = {
                filterGroup: filterContainer.closest('[data-filtergroup]')?.getAttribute('data-filtergroup')
              };
              this.call();
            }
          }
          // Every other filter button
          else if (target.classList.contains(this.identifier.filterRadio.substring(1)) ||
            target.classList.contains(this.identifier.filterCheckbox.substring(1))) {
            const showAllCheckbox = target
              .closest(this.identifier.filterOptionContainer)
              .querySelector(this.identifier.filterShowAllCheckbox);
            this.onClick(keyboardEvent);
            showAllCheckbox.checked = false;
            showAllCheckbox.disabled = false;

            // Keep focus on the selected filter element
            this.saveFocusContext(target);
            this.call();
          }
        }
      }
    }.bind(this));
  }

  prepareFilter() {
    this.prepareCheckboxes();

    // Open selected filter sections
    if (this.filter.length > 0 && this.openFilterOnLoad) {
      this.toggleFilterVisibility();

      this.filter.forEach(function (filterName) {
        let filterFieldset = this.filterElement.querySelector('[data-filtergroup="' + filterName + '"]');
        let filter = filterFieldset.querySelector(this.identifier.filterOptionContainer);

        filter.classList.toggle(this.identifier.hideElement.substring(1));
      }.bind(this));
    }
  }

  prepareCheckboxes() {
    this.filterElement.querySelectorAll(this.identifier.filterOptionContainer).forEach(function (filterOptionContainer) {
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

  /**
   * Store context of the focused element before AJAX call
   *
   * @param {HTMLElement} element - The filter input element to save
   */
  saveFocusContext(element) {
    if (!element) return;

    this.focusType = 'input';
    this.lastFocusedElement = {
      filterGroup: element.closest('[data-filtergroup]')?.getAttribute('data-filtergroup'),
      value: element.value,
      type: element.type,
      name: element.name
    };
  }

  /**
   * Restore focus after AJAX update
   *
   * After DOM is rebuilt by AJAX, this method restores focus to the previously
   * focused element to maintain context for keyboard and screen reader users.
   */
  restoreFocus() {
    if (!this.lastFocusedElement && !this.focusType) return;

    let targetElement = null;

    // Focus on "show filter" button after reset
    if (this.focusType === 'showButton') {
      targetElement = this.filterElement.querySelector(this.identifier.showFilterButton);
    }
    // Focus on fieldset when clicking "all" button with other filters still active
    else if (this.focusType === 'fieldset' && this.lastFocusedElement) {
      const { filterGroup } = this.lastFocusedElement;
      if (filterGroup) {
        targetElement = this.filterElement.querySelector(`[data-filtergroup="${filterGroup}"]`);
      }
    }
    // Keep focus on filter option when it's not "all"
    else if (this.focusType === 'input' && this.lastFocusedElement) {
      const { filterGroup, value, type, name } = this.lastFocusedElement;
      if (filterGroup) {
        const fieldset = this.filterElement.querySelector(`[data-filtergroup="${filterGroup}"]`);
        if (fieldset) {
          targetElement = fieldset.querySelector(`input[type="${type}"][name="${name}"][value="${value}"]`);
        }
      }
    }

    // Set focus
    if (targetElement) {
      setTimeout(() => {
        targetElement.focus();
      }, 100);
    }

    // Reset context
    this.lastFocusedElement = null;
    this.focusType = null;
  }

  call(paginationPage) {
    let pid = this.filterElement.querySelector('input[name="tx_in2studyfinder_filter[pluginInformation][pid]"]').value;
    let language = this.filterElement.querySelector('input[name="tx_in2studyfinder_filter[pluginInformation][languageUid]"]').value;
    let paginationArgument = '';
    let instanceId = this.studyfinderElement.getAttribute('data-in2studyfinder-instance-id')

    if (typeof paginationPage === 'undefined') {
      paginationPage = 1;
    }

    if (typeof paginationPage !== 'undefined') {
      paginationArgument = '&tx_in2studyfinder_filter[currentPage]=' + paginationPage;
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

    filterFieldSets.forEach(function (filterFieldSet) {
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
      selectionString += '&page=' + paginationPage;
    }

    if (selectionString !== '') {
      selectionString = '#' + selectionString;
    }

    const url = location.protocol + '//' + location.host + location.pathname + (location.search ? location.search : '') + selectionString;
    history.pushState({}, '', url);
  };

  updateFilterByHashArguments(hashArguments) {
    let page = 1;

    hashArguments.forEach(function (hashArgument) {
      // If argument page is set
      if (hashArgument.name === 'page') {
        page = hashArgument.values[0];
      } else {
        if (this.filterElement.querySelector('[data-filtergroup="' + hashArgument.name + '"]') !== null) {
          // Set the selected filters
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
    this.filterElement.querySelector(this.identifier.filterSectionContainer).addEventListener('click', function (evt) {
        let target = evt.target;

        if (target.tagName === 'INPUT') {
          // Focus handling for "all" filter
          if (target.classList.contains(this.identifier.filterShowAllCheckbox.substring(1))) {
            this.onClick(evt);
            let filterContainer = target.closest(this.identifier.filterOptionContainer);
            this.resetFilter(filterContainer);

            // Check if other filters are active
            if (this.filter.length === 0) {
              // No filters left: close mask and focus moves to "show filter" button
              this.toggleFilterVisibility();
              setTimeout(() => {
                this.filterElement.querySelector(this.identifier.showFilterButton).focus();
              }, 50);
            } else {
              // Other filters still active: focus moves to parent fieldset
              this.focusType = 'fieldset';
              this.lastFocusedElement = {
                filterGroup: filterContainer.closest('[data-filtergroup]')?.getAttribute('data-filtergroup')
              };
              this.call();
            }
          }
          // Every other filter button
          else if (target.classList.contains(this.identifier.filterCheckbox.substring(1)) || target.classList.contains(this.identifier.filterRadio.substring(1))) {
            let showAllCheckbox = target.closest(this.identifier.filterOptionContainer).querySelector(this.identifier.filterShowAllCheckbox);
            this.onClick(evt);
            showAllCheckbox.checked = false;
            showAllCheckbox.disabled = false;

            // Keep focus on the selected element
            this.saveFocusContext(target);
            this.call();
          }
        }
      }.bind(this)
    );
  }

  toggleFilterVisibility() {
    // Toggle fieldset visibility
    let filterFieldSets = this.filterElement.querySelectorAll(this.identifier.filterSection);

    for (let i = 0; i < filterFieldSets.length; i++) {
      filterFieldSets[i].classList.toggle(this.identifier.hideElement.substring(1));
    }

    // Toggle button visibility
    this.filterElement.querySelector(this.identifier.showFilterButton).classList.toggle(this.identifier.hideElement.substring(1));
    this.filterElement.querySelector(this.identifier.hideFilterButton).classList.toggle(this.identifier.hideElement.substring(1));

    // Add skip filter link
    const skipToResults = document.querySelector('.skip-link[href="#results"]');
    if (skipToResults) {
      // Show skip link when filter is open
      const filterIsOpen = !this.filterElement
        .querySelector(this.identifier.hideFilterButton)
        .classList.contains(this.identifier.hideElement.substring(1));

      if (filterIsOpen) {
        skipToResults.removeAttribute('hidden');
      } else {
        skipToResults.setAttribute('hidden', 'hidden');
      }
    }
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
   * Check if a given filter element has any option selected
   *
   * @param {HTMLElement} filterOptionContainer - The filter container element
   * @returns {boolean} True if at least one option is selected
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

  onClick() {
  }
}

export {Filter}
