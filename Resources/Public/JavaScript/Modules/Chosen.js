(function In2studyfinder() {
	function Chosen(dom) {

		this.initializeChosen = function() {
			$(dom).find('.in2studyfinder-js-fast-select').chosen({
				no_results_text: 'Keine Treffer',
				search_contains: true
			}).on('change', function() {
				$(this).parent('form').submit();
			});
		};
	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.Chosen = Chosen;
})();
