(function In2studyfinder() {
	'use strict';

	function FilterHandling(dom) {

		var urlHandling = new window.In2studyfinder.UrlHandling(dom);
		var uiBehaviour = new window.In2studyfinder.UiBehaviour(dom);
		var start = new window.In2studyfinder.Start();
		var filterHandling = this;

		this.init = function() {
			filterHandling.resetAllFilterOptions();

			// "Alle" Checkbox unchecken wenn eine andere geklickt wird
			// und wieder checken, wenn keine mehr markiert ist
			$('.in2studyfinder-js-checkbox').change(function () {
				uiBehaviour.uncheckAllCheckbox($(this));
				filterHandling.filterChanged();
			});

			//alle Checkboxen unchecken, wenn "Alle" geklickt wird
			$('.in2studyfinder-js-checkbox-all').change(function () {
				uiBehaviour.uncheckOtherCheckboxes($(this));
				filterHandling.filterChanged();
			});
		};

		this.filterChanged = function(paginationPage) {
			var studyFinderForm = $('.in2studyfinder-js-filter');
			var url = urlHandling.removeUrlParam('cHash', studyFinderForm.attr('action'));
			if (paginationPage) {
				url += '&tx_in2studyfinder_pi1%5B%40widget_0%5D%5BcurrentPage%5D=' + paginationPage;
			}
			$.ajax({
				type: 'POST',
				url: url,
				data: studyFinderForm.serialize(),
				beforeSend: function () {
					uiBehaviour.enableLoading();
				},
				success: function (data) {
					urlHandling.saveSelectedOptionsToUrl(paginationPage);
					$('.in2studyfinder').html($(data).find('.in2studyfinder').html());
				},
				error: function () {
				},
				complete: function () {
					start.init();
					uiBehaviour.openPreviouslyOpenedFilterSections();
					uiBehaviour.disbaleLoading();
				},
				cache: false
			});
		};

		this.isAnyFilterSet = function () {
			var isFilterSet = false;

			$.each($('.in2studyfinder-js-checkbox'), function() {
				if ($(this).prop('checked')) {
					isFilterSet = true;
				}
			});

			return isFilterSet;
		};

		this.resetAllFilterOptions = function() {
			$('.in2studyfinder-js-reset-filter-options').on('click', function () {
				if (filterHandling.isAnyFilterSet()) {
					uiBehaviour.resetAllFilterCheckboxes();
					filterHandling.filterChanged();
				} else {
					uiBehaviour.hideFilters();
					uiBehaviour.toggleShowFiltersButton();
				}

			});
		};


	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.FilterHandling = FilterHandling;
})();
