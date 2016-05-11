(function In2studyfinder() {
	function UiBehaviour(dom) {

		var uiBehaviour = this;

		this.enableLoading = function() {
			$(dom).find('.in2studyfinder').addClass('in2studyfinder--loading');
		};

		this.disbaleLoading = function() {
			$(dom).find('.in2studyfinder').removeClass('in2studyfinder--loading');
		};

		this.toggleOptionFormVisibility = function() {
			$('.in2studyfinder-js-show-filter-options').on('click', function() {
				$(this).toggleClass('hide');
				$(this).siblings().toggleClass('hide');
				$(this).parent().siblings('fieldset').toggleClass('hide');
			});
		};

		this.toggleOptionSectionVisibility = function() {
			$('.in2studyfinder-js-option-legend').on('click', function() {
				$(this).siblings().toggleClass('hide');
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
					$(element).siblings('.in2studyfinder-js-checkbox-all').prop("checked", true).prop("disabled", true);
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
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.UiBehaviour = UiBehaviour;
})();

