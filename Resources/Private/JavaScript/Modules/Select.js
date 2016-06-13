(function In2studyfinder() {
		function Select(dom) {

			this.initializeSelect = function() {
				$('.in2studyfinder-js-fast-select').select2({
					matcher: function(params, data) {
						var status = false;
						var term = params.term;
						var text = data.text;

						if ($.trim(term) === '') {
							status = true;
						}

						if (data.id !== '' && $.trim(term) !== '') {
							var element = data.element;
							var keywords = $(element).attr('alt');
							var keywordArray = keywords.split(',');

							$.each(keywordArray, function(index, keyword) {
								keyword = keyword.trim();
								if (
									text.toUpperCase().indexOf(term.toUpperCase()) > -1 ||
									keyword.toUpperCase().indexOf(term.toUpperCase()) > -1
								) {
									status = true
								}
							});
						}

						if (status) {
							return data;
						} else {
							return null;
						}

					},
					placeholder: 'select degree program or enter keyword',
					allowClear: false,
					language: 'de'
				});
			};

			$('.in2studyfinder-js-fast-select').on('change', function() {
				$(this).parent('form').submit();
			});

			/** @todo better language handling */
			if ($('html').attr('lang') === 'de') {
				$('.in2studyfinder-js-fast-select').attr("data-placeholder", "Studiengang wählen oder Suchbegriff eingeben");
			} else {
				$('.in2studyfinder-js-fast-select').attr("data-placeholder", "select degree program or enter keyword");
			}
		}

		// export to global scope
		if (!window.In2studyfinder) {
			window.In2studyfinder = {};
		}

		window.In2studyfinder.Select = Select;
	})
();
