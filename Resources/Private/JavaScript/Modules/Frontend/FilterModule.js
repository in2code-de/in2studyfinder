define(['TYPO3/CMS/In2studyfinder/Utility/UiUtility', 'TYPO3/CMS/In2studyfinder/Utility/UrlUtility', 'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility'], function(UiUtility, UrlUtility, AjaxUtility) {
	'use strict';

	var FilterModule = {
		identifiers: {
			in2studyfinderContainer: '.in2studyfinder',
			filterForm: '.js-in2studyfinder-filter',
			filterContainer: '.js-in2studyfinder-filter-options',
			filterFieldset: '.js-in2studyfinder-filter-section',
			filterLegend: '.js-in2studyfinder-filter-legend',
			filterCheckbox: '.in2studyfinder-js-checkbox',
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
		FilterModule.setEventListener();
		FilterModule.prepareFilter();

	};

	FilterModule.prepareFilter = function() {
		FilterModule.prepareCheckboxes();

		// open selected filter sections
		if (FilterModule.filter.length > 0) {
			FilterModule.filter.forEach(function(value) {
				FilterModule.toggleFilterVisibility();

				var filterFieldset = document.querySelector('[data-filtergroup="' + value + '"]');
				var filter = filterFieldset.querySelector(FilterModule.identifiers.filterContainer);

				UiUtility.toggleClassForElement(filter, FilterModule.identifiers.isHidden.substr(1));
			});
		}
	};

	/**
	 * sets event listeners
	 */
	FilterModule.setEventListener = function() {
		// hide filter button
		var hideFilter = document.querySelector(FilterModule.identifiers.hideFilterButton);
		hideFilter.addEventListener('click', FilterModule.toggleFilterVisibility);

		// show filter button
		var showFilter = document.querySelector(FilterModule.identifiers.showFilterButton);
		showFilter.addEventListener('click', FilterModule.toggleFilterVisibility);

		// toggle filter section visibility
		FilterModule.setFilterVisibilityEventListener();

		// set eventListener for filter checkboxes
		FilterModule.setFilterCheckboxEventListener();
	};

	FilterModule.setFilterCheckboxEventListener = function() {
		// set eventlistener für alle nicht disableten checkboxen
		// wenn eine all checkbox geklickt wird wird der filter zurückgesetzt
		// wenn eine andere checkbox geklickt wird wird dieser filter gesetzt

		document.querySelector('.c-in2studyfinder-filter__sections').addEventListener('click', function(evt) {
				var target = evt.target;

				if (target.tagName === 'INPUT') {
					// if an show all checkbox is clicked
					if (target.classList.contains(FilterModule.identifiers.filterCheckboxAll.substr(1))) {
						var filterContainer = target.parentNode;
						FilterModule.resetFilter(filterContainer);
						// @todo remove filter from selected filter list!
					}

					// if an specific filter checkbox is clicked
					if (target.classList.contains(FilterModule.identifiers.filterCheckbox.substr(1))) {
						var showAllCheckbox = target.parentNode.querySelector(FilterModule.identifiers.filterCheckboxAll);
						showAllCheckbox.checked = false;
						showAllCheckbox.disabled = false;
						// @todo add filter to selected filter list!
					}

					FilterModule.updateFilter();
				}
			}
		);
	};

	FilterModule.resetFilter = function(filterContainer) {
		var showAllCheckbox = filterContainer.querySelector(FilterModule.identifiers.filterCheckboxAll);
		var filterCheckboxes = filterContainer.querySelectorAll(FilterModule.identifiers.filterCheckbox);

		showAllCheckbox.checked = true;
		showAllCheckbox.disabled = true;

		filterCheckboxes.forEach(function(checkbox) {
			checkbox.checked = false;
		});
	};


	FilterModule.updateFilter = function() {
		var in2studyfinderContainer = document.querySelector(FilterModule.identifiers.in2studyfinderContainer);
		var filterForm = document.querySelector(FilterModule.identifiers.filterForm);
		var pluginUid = in2studyfinderContainer.getAttribute('data-plugin-uid');
		var sysLanguageUid = in2studyfinderContainer.getAttribute('data-in2studyfinder-language');
		var paginationPage = 1;

		var pluginUidArgument = '';
		var languageArgument = '';
		var paginationArgument = '';

		if (typeof pluginUid !== 'undefined') {
			pluginUidArgument = '&ce=' + pluginUid;
		}

		if (typeof sysLanguageUid !== 'undefined' && sysLanguageUid !== null) {
			languageArgument = '&L=' + sysLanguageUid;
		}

		if (typeof paginationPage !== 'undefined') {
			paginationArgument = '&tx_in2studyfinder_pi1[@widget_0][currentPage]=' + paginationPage;
		}

		var url = '/?type=1308171055&studyFinderAjaxRequest=1' + pluginUidArgument + languageArgument + paginationArgument;

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState === 1) {
				UiUtility.enableLoader();
			}

			if (this.readyState === 4 && this.status === 200) {
				//FilterModule.setSelectedFilterToUrl();
				// @todo Save selected filter to url

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
	 *
	 * @todo
	 */
	FilterModule.setSelectedFilterToUrl = function(paginationPage) {
		var selectionValues = {};
		var selectionString = '';
		var form = document.querySelector(FilterModule.identifiers.filterForm);
		var newSelectedOptions = form.querySelectorAll(FilterModule.identifiers.filterCheckbox + ':checked');

		newSelectedOptions.forEach(function(checkbox) {
			console.log(checkbox);
		});
		console.log(newSelectedOptions);
		var selectedOptions = $('.js-in2studyfinder-filter').find('input.in2studyfinder-js-checkbox:checked');
		$(selectedOptions).each(function() {
			var filterGroupAbbreviation = $(this).closest('fieldset').data('filtergroup');
			if (selectionValues[filterGroupAbbreviation] === undefined) {
				selectionValues[filterGroupAbbreviation] = [];
			}
			selectionValues[filterGroupAbbreviation].push($(this).val());
		});

		$(selectionValues).each(function(key, values) {
			$.each(values, function(filterKey, value) {
				selectionString += filterKey + '--';
				$.each(value, function(key, value) {
					selectionString += value + '+';
				});
				selectionString = selectionString.replace(/\+$/, '');
				selectionString += '__';
			});
			selectionString = selectionString.replace(/__$/, '');
		});

		if (paginationPage) {
			selectionString += 'page=' + paginationPage;
		}
		window.location = location.protocol + '//' + location.host + location.pathname + (location.search ? location.search : '') + '#' + selectionString;
	};

	/**
	 * Load Selected Options from Url
	 *
	 * @todo
	 */
	FilterModule.getSelectedFilterFromUrl = function() {
		var filterHash = window.location.hash.split('#');

		if (1 in filterHash) {
			filterHash = filterHash[1];
			var paginationPage;
			if (filterHash.indexOf('page=') !== -1) {
				paginationPage = filterHash.split('page=')[1];
				filterHash = filterHash.split('page=')[0];
			}

			if (filterHash !== '') {

				var filterParts = filterHash.split('__');
				$(filterParts).each(function(key, values) {
					var sectionSplit = '--';
					var selections = values.split(sectionSplit);
					var filterGroup = values.substr(0, values.indexOf(sectionSplit));
					var selectedOptions = selections[1].split('+');

					$(selectedOptions).each(function(key, value) {
						$('#' + filterGroup + '_' + value).prop('checked', true);
					});
				});
			}

			return paginationPage;
		}
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

		var filterButtons = document.querySelectorAll(FilterModule.identifiers.filterFieldset);
		filterButtons.forEach(function(filterButton) {
			filterButton.querySelector(FilterModule.identifiers.filterLegend).addEventListener('click', function() {
				var filter = filterButton.querySelector(FilterModule.identifiers.filterContainer);

				UiUtility.toggleClassForElement(filter, FilterModule.identifiers.isHidden.substr(1));
			});
		});
	};

	/**
	 * Toggles the filter fieldset visibility
	 */
	FilterModule.toggleFilterVisibility = function() {

		// toggle fieldset Visibility
		document.querySelectorAll(FilterModule.identifiers.filterFieldset).forEach(function(filterFieldset) {
			UiUtility.toggleClassForElement(filterFieldset, FilterModule.identifiers.hideElement.substr(1));
		});

		// toggle button Visibility
		var showFilterButton = document.querySelector(FilterModule.identifiers.showFilterButton);
		var hideFilterButton = document.querySelector(FilterModule.identifiers.hideFilterButton);
		UiUtility.toggleClassForElement(showFilterButton, FilterModule.identifiers.hideElement.substr(1));
		UiUtility.toggleClassForElement(hideFilterButton, FilterModule.identifiers.hideElement.substr(1));
	};

	/**
	 * removes checked value from the checkboxes where not needed
	 */
	FilterModule.prepareCheckboxes = function() {
		document.querySelectorAll(FilterModule.identifiers.filterContainer).forEach(function(filterContainer) {
			var filterStatus = FilterModule.isFilterSet(filterContainer);

			if (filterStatus) {
				FilterModule.filter.push(filterContainer.parentNode.getAttribute('data-filtergroup'));
				var showAllCheckbox = filterContainer.querySelector(FilterModule.identifiers.filterCheckboxAll);
				showAllCheckbox.checked = false;
				showAllCheckbox.disabled = false;
			}
		});
	};

	/**
	 * checks if an given filter element is set
	 *
	 * @param filterContainer
	 * @returns {boolean}
	 */
	FilterModule.isFilterSet = function(filterContainer) {
		var status = false;

		filterContainer.querySelectorAll(FilterModule.identifiers.filterCheckbox).forEach(function(checkbox) {
			if (checkbox.checked) {
				status = true;
			}
		});

		return status;
	};

	return FilterModule;
})
;
