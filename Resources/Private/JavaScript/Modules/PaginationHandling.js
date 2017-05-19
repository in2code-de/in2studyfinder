(function In2studyfinder() {
	'use strict';

	function PaginationHandling(dom) {

		var paginationHandling = this;
		var filterHandling = new window.In2studyfinder.FilterHandling(dom);
		var urlHandling = new window.In2studyfinder.UrlHandling(dom);

		this.init = function() {
			$('.js-get-by-ajax').on('click', function(e) {
				e.preventDefault();
				paginationHandling.callPagination($(this));
			});
		};


		this.callPagination = function(element) {
			var destination = urlHandling.removeUrlParam('cHash', element.attr('href'));
			var targetPage = 1;
			if (destination.indexOf('&') !== -1 || destination.indexOf('?') !== -1) {
				destination.replace('?', '&').split('&').forEach(function(value) {
					if (value.match(/tx_in2studyfinder_pi1%5B%40widget_0%5D%5BcurrentPage%5D=/)) {
						targetPage = value.replace('tx_in2studyfinder_pi1%5B%40widget_0%5D%5BcurrentPage%5D=', '');
					}
				});
			}

			filterHandling.filterChanged(targetPage);
		};
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.PaginationHandling = PaginationHandling;
})();
