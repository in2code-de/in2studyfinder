/**
 * In2studyfinder module
 *
 * @module In2studyfinder
 */
(function In2studyfinder() {
	'use strict';
	var readFilterFromUrl = true;

	function Start() {
		var dom = $('.in2studyfinder');

		/**
		 * initialize stuff
		 */
		this.init = function () {
			if (dom.length > 0) {
				var select = new window.In2studyfinder.Select(dom);
				var filterHandling = new window.In2studyfinder.FilterHandling(dom);
				var urlHandling = new window.In2studyfinder.UrlHandling(dom);
				var uiBehaviour = new window.In2studyfinder.UiBehaviour(dom);
				var paginationHandling = new window.In2studyfinder.PaginationHandling(dom);

				select.initializeSelect();
				filterHandling.init();
				paginationHandling.init();
				uiBehaviour.init();

				if (readFilterFromUrl && document.querySelector('.in2studyfinder__list') !== null) {
					readFilterFromUrl = false;
					var paginationPage = urlHandling.loadSelectedOptionsFromUrl();
					filterHandling.filterChanged(paginationPage);
				}
			}
		};
	}

// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.Start = Start;
})
();

jQuery(document).ready(function () {
	'use strict';
	var start = new window.In2studyfinder.Start();
	start.init();
});
