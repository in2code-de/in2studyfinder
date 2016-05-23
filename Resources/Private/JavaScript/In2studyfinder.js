/**
 * In2studyfinder module
 *
 * @module In2studyfinder
 */
(function In2studyfinder() {

	/**
	 * @type {*|jQuery|HTMLElement}
	 */
	var dom = $('.in2studyfinder');
	var readFilterFromUrl = true;

	function Start() {

		/**
		 * initialize stuff
		 */
		this.init = function() {

			var chosen = new window.In2studyfinder.Chosen(dom);
			var filterHandling = new window.In2studyfinder.FilterHandling(dom);
			var urlHandling = new window.In2studyfinder.UrlHandling(dom);
			var uiBehaviour = new window.In2studyfinder.UiBehaviour(dom);
			var paginationHandling = new window.In2studyfinder.PaginationHandling(dom);

			chosen.initializeChosen();
			filterHandling.init();
			paginationHandling.init();
			uiBehaviour.checkboxHandling();

			if (readFilterFromUrl) {
				readFilterFromUrl = false;
				if (window.location.hash) {
					var paginationPage = urlHandling.loadSelectedOptionsFromUrl();
					filterHandling.filterChanged(paginationPage);
				}
			}

			$('.select2').select2({
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
					placeholder: "Select an Studycourse",
					allowClear: true
				}
			)
			;

		};
	}

// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.Start = Start;
})
();

jQuery(document).ready(function() {
	var start = new window.In2studyfinder.Start();
	start.init();
});
