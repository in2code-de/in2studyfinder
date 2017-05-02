(function In2studyfinder() {
	function Select(dom) {

		/**
		 * Init Select2
		 */
		this.initializeSelect = function () {

			var query = {};

			$('.in2studyfinder-js-fast-select').select2({
				matcher: function (params, data) {
					query = params;
					var select = new window.In2studyfinder.Select(dom);
					return select.matcher(params, data);
				},
				sorter: function (results) {
					/* @todo: optimise sorting */
					var sorted = results.slice(0);

					sorted.sort(function (first, second) {
						if (query.term && query.term !== '') {
							var firstTextPosition = first.text.toUpperCase().indexOf(
								query.term.toUpperCase()
							);
							var secondTextPosition = second.text.toUpperCase().indexOf(
								query.term.toUpperCase()
							);


							if (firstTextPosition === -1) {
								firstTextPosition = 100;
							}

							if (secondTextPosition === -1) {
								secondTextPosition = 100;
							}

							return firstTextPosition - secondTextPosition;
						}
					});

					return sorted;
				},
				allowClear: false,
				placeholder: 'select degree program or enter keyword',
				language: 'de'
			});
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

		$('.in2studyfinder-js-fast-select').on('select2:select', function () {
			var obj = $('.in2studyfinder-js-fast-select').select2('data');
			var url = obj[0].element.dataset.url;

			if (url.length) {
				window.location.href = url;
			}
		});

		/** @todo better language handling for Placeholder */
		if ($('html').attr('lang') === 'de') {
			$('.in2studyfinder-js-fast-select').attr('data-placeholder', 'Studiengang wählen oder Suchbegriff eingeben');
		} else {
			$('.in2studyfinder-js-fast-select').attr('data-placeholder', 'select degree programme or enter keyword');
		}
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.Select = Select;
})
();
