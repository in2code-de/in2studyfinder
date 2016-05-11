(function In2studyfinder() {
	function UrlHandling(dom) {

		var urlHandling = this;

		/**
		 * Remove Param
		 */
		this.removeUrlParam = function(key, sourceURL) {
			var rtn = sourceURL.split("?")[0],
				param,
				params_arr = [],
				queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
			if (queryString !== "") {
				params_arr = queryString.split("&");
				for (var i = params_arr.length - 1; i >= 0; i -= 1) {
					param = params_arr[i].split("=")[0];
					if (param === key) {
						params_arr.splice(i, 1);
					}
				}
				rtn = rtn + "?" + params_arr.join("&");
			}
			return rtn;
		};

		/**
		 * Save Selected Options to Url
		 */
		this.saveSelectedOptionsToUrl = function(paginationPage) {
			var selectionValues = {};
			var selectionString = '';
			var selectedOptions = $('.in2studyfinder-js-filter').find('input.in2studyfinder-js-checkbox:checked');
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
				selectionString += 'p' + paginationPage;
			}
			window.location = location.protocol + '//' + location.host + location.pathname + (location.search ? location.search : '') + '#' + selectionString;
		};

		/**
		 * Load Selected Options from Url
		 */
		this.loadSelectedOptionsFromUrl = function() {
			var filterHash = window.location.hash.split('#');
			if (1 in filterHash) {
				filterHash = filterHash[1];
				var paginationPage;
				if (filterHash.indexOf('p') !== -1) {
					paginationPage = filterHash.split('p')[1];
					filterHash = filterHash.split('p')[0];
				}

				var filterParts = filterHash.split('__');
				$(filterParts).each(function(key, values) {
					var sectionSplit = '--';
					var selections = values.split(sectionSplit);
					var filterGroup = values.substr(0, values.indexOf(sectionSplit));
					var selectedOptions = selections[1].split('+');
					$(selectedOptions).each(function(key, value) {
						$('#' + filterGroup + '_' + value).prop("checked", true);
					});
				});

				return paginationPage;
			}
		}
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.UrlHandling = UrlHandling;
})();
