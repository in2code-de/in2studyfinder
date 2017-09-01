(function In2studyfinder() {
	'use strict';

	function UiBehaviour(dom) {

		var uiBehaviour = this;

		this.init = function() {
			this.checkboxHandling();
		};

		this.enableLoading = function() {
			$('.in2js-in2studyfinder-loader').addClass('in2studyfinder-loader--active');
		};

		this.disbaleLoading = function() {
			$('.in2js-in2studyfinder-loader').removeClass('in2studyfinder-loader--active');
		};

		this.toggleOptionFormVisibility = function() {
			// “Start.init()” is called multiple times, so we’ve to ensure that
			// it’s not going to add the same event handler more than once.
			$('.js-in2studyfinder-filter-button-show')
				.off('click')
				.on('click', function() {
					uiBehaviour.toggleShowFiltersButton();
					$('.js-in2studyfinder-filter-section').toggleClass('hide');
				});
		};

		this.toggleOptionSectionVisibility = function() {
			// “Start.init()” is called multiple times, so we’ve to ensure that
			// it’s not going to add the same event handler more than once.
			$('.js-in2studyfinder-filter-legend')
				.off('click')
				.on('click', function() {
					$(this)
						.toggleClass('opened')
						.siblings()
							.toggleClass('hide');
				});
		};

		this.openPreviouslyOpenedFilterSections = function() {
			var filtered = false;
			$('.js-in2studyfinder-filter').find('input[type=checkbox]:checked:enabled').each(function () {
				$(this).siblings('.in2studyfinder-js-checkbox-all').prop('checked', false).prop('disabled', false);
				var parent = $(this).parents('.js-in2studyfinder-filter-options');

				if (!parent.hasClass('opened')) {
					parent.removeClass('hide');
					filtered = true;
				}
			});
			if (filtered) {
				$('.js-in2studyfinder-filter-button-show').click();
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
			$.each($('.js-in2studyfinder-filter-section'), function () {
				$(this).addClass('hide');
			});
		};

		this.toggleShowFiltersButton = function () {

			var showButton = $('.js-in2studyfinder-filter-button-show');
			var resetButton = $('.js-in2studyfinder-filter-button-reset');

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
