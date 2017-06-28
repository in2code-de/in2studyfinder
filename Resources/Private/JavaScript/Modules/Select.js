(function In2studyfinder() {
	'use strict';

	function Select(dom) {

		var select = $('.in2studyfinder-js-fast-select');

		/**
		 * Init Select2
		 */
		this.initializeSelect = function () {
			var query = {};

			this.updatePlaceholderLabel();

			select.select2({
				matcher: function (params, data) {
					query = params;
					var select = new window.In2studyfinder.Select(dom);
					return select.matcher(params, data);
				},
				sorter: function (results) {
					var sorted = results.slice(0);

					sorted.sort(function (first, second) {
						if (first.text.toUpperCase() < second.text.toUpperCase()) {
							return -1;
						}

						if (first.text.toUpperCase() > second.text.toUpperCase()) {
							return 1;
						}

						return 0;
					});

					return sorted;
				},
				allowClear: false,
				placeholder: 'select degree program or enter keyword',
				language: 'de'
			});

			this.redirectOnSelect();
		};

		/**
		 * Matcher for Recursive Search in select2
		 * @param params
		 * @param data
		 * @returns {*}
		 */
		this.matcher = function (params, data) {
			if ($.trim(params.term) === '') {
				return data;
			}

			var original = data.text.toUpperCase();
			var term = params.term.toUpperCase();
			var element = data.element;
			var keywords = $(element).attr('alt');
			var status = false;

			if (keywords !== undefined && keywords !== '') {
				var keywordArray = keywords.split(',');
				// Search Keywords
				$.each(keywordArray, function (index, keyword) {
					keyword = keyword.trim();
					if (
						data.text.toUpperCase().indexOf(term.toUpperCase()) > -1 ||
						keyword.toUpperCase().indexOf(term.toUpperCase()) > -1
					) {
						status = true;
					}
				});
			}

			if (original.indexOf(term) > -1 || status === true) {
				return data;
			}

			if (data.children && data.children.length > 0) {
				var match = $.extend(true, {}, data);

				for (var c = data.children.length - 1; c >= 0; c--) {
					var child = data.children[c];

					var matches = this.matcher(params, child);

					if (matches === null) {
						match.children.splice(c, 1);
					}
				}


				if (match.children.length > 0) {
					return match;
				}

				return this.matcher(params, match);
			}

			return null;
		};

		/**
		 * Redirect to the selected Studycourse on Select
		 */
		this.redirectOnSelect = function () {
			select.on('select2:select', function () {
				var obj = select.select2('data');
				var url = obj[0].element.dataset.url;

				if (url.length) {
					window.location.href = url;
				}
			});
		};

		/**
		 * Update Placeholder Label if Language is de
		 */
		this.updatePlaceholderLabel = function () {
			/** @todo better language handling for Placeholder */
			if ($('html').attr('lang') === 'de') {
				select.attr('data-placeholder', 'Studiengang wählen oder Suchbegriff eingeben');
			} else {
				select.attr('data-placeholder', 'Select degree program or enter keyword');
			}
		};
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.Select = Select;
})
();
