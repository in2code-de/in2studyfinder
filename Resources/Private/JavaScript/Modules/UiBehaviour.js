(function In2studyfinder() {
	'use strict';

	function UiBehaviour(dom) {

		var uiBehaviour = this;

		this.init = function() {
			$('.in2studyfinder__fast-select').removeClass('hide');
			$('.in2studyfinder__filter').removeClass('hide');

			this.checkboxHandling();
		};

		this.enableLoading = function() {
			$(dom).addClass('in2studyfinder__loading');
		};

		this.disbaleLoading = function() {
			$(dom).removeClass('in2studyfinder__loading');
		};

		this.toggleOptionFormVisibility = function() {
			$('.in2studyfinder-js-show-filter-options').on('click', function() {
				$(this).toggleClass('hide');
				$(this).siblings().toggleClass('hide');
				$('.in2studyfinder-js-filter-options').children('.in2studyfinder-js-option-section').toggleClass('hide');
			});
		};

		this.toggleOptionSectionVisibility = function() {
			$('.in2studyfinder-js-option-legend').on('click', function() {
				$(this)
					.toggleClass('opened')
					.siblings().toggleClass('hide');
			});
		};

		this.openPreviouslyOpenedFilterSections = function() {
			var filtered = false;
			$('.in2studyfinder-js-filter').find('input[type=checkbox]:checked:enabled').each(function () {
				$(this).siblings('.in2studyfinder-js-checkbox-all').prop('checked', false).prop('disabled', false);
				var parent = $(this).parents('.in2studyfinder-js-accordion');

				if (!parent.hasClass('opened')) {
					parent.removeClass('hide');
					filtered = true;
				}
			});
			if (filtered) {
				$('.in2studyfinder-js-show-filter-options').click();
			}
		};

		this.resetAllFilterCheckboxes = function() {
			$.each($('.in2studyfinder-js-checkbox'), function() {
				$(this).prop('checked', false);
			});
			$.each($('.in2studyfinder-js-checkbox-all'), function() {
				$(this).prop('checked', false);
			});
		};

		this.uncheckAllCheckbox = function(element) {
			if ($(element).prop('checked')) {
				$(element).siblings('.in2studyfinder-js-checkbox-all').prop('checked', false).prop('disabled', false);
			} else {
				if ($(element).siblings('.in2studyfinder-js-checkbox:checkbox:checked').length === 0) {
					$(element).siblings('.in2studyfinder-js-checkbox-all').prop('checked', true).prop('disabled', true);
				}
			}
		};

		this.uncheckOtherCheckboxes = function(element) {
			if ($(element).prop('checked')) {
				$(element).siblings('.in2studyfinder-js-checkbox:checkbox:checked').each(function() {
					$(this).prop('checked', false);
				});
				$(element).prop('disabled', true);
			}
		};

		this.checkboxHandling = function() {
			uiBehaviour.toggleOptionFormVisibility();
			uiBehaviour.toggleOptionSectionVisibility();
		};

		this.hideFilters = function () {
			$.each($('.in2studyfinder-js-option-section'), function () {
				$(this).addClass('hide');
			});
		};

		this.toggleShowFiltersButton = function () {

			var showButton = $('.in2studyfinder-js-show-filter-options');
			var resetButton = $('.in2studyfinder-js-reset-filter-options');

			if (showButton.hasClass('hide')) {
				showButton.removeClass('hide');
				resetButton.addClass('hide');
			} else if (resetButton.hasClass('hide')) {
				resetButton.removeClass('hide');
				showButton.addClass('hide');
			}
		};
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.UiBehaviour = UiBehaviour;
})();

