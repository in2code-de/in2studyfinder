(function In2studyfinder() {
	function PaginationHandling(dom) {

		var paginationHandling = this;
		var uiBehaviour = new window.In2studyfinder.UiBehaviour(dom);
		var urlHandling = new window.In2studyfinder.UrlHandling(dom);
		var start = new window.In2studyfinder.Start();

		this.init = function() {
			$('.js-get-by-ajax').on('click', function(e) {
				e.preventDefault();
				paginationHandling.callPagination($(this));
			});
		};


		this.callPagination = function(element) {
			var destination = urlHandling.removeUrlParam('cHash', element.attr('href'));
			var targetPage = 1;
			destination.split('&').forEach(function(value) {
				if (value.match(/tx_in2studyfinder_studycourse%5B%40widget_0%5D%5BcurrentPage%5D=/)) {
					targetPage = value.replace('tx_in2studyfinder_studycourse%5B%40widget_0%5D%5BcurrentPage%5D=', '');
				}
			});

			$.ajax({
				type: "GET",
				url: destination,
				beforeSend: function() {
					uiBehaviour.enableLoading();
					urlHandling.saveSelectedOptionsToUrl(targetPage);
				},
				success: function(data) {
					$('.in2studyfinder').html($(data).find('.in2studyfinder'));
					window.scrollTo(0, 0);
				},
				error: function() {
					alert('fail')
				},
				complete: function() {
					start.Init();
					uiBehaviour.openPreviouslyOpenedFilterSections();
					uiBehaviour.disbaleLoading();
				},
				cache: false

			});
		}
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.PaginationHandling = PaginationHandling;
})();
