(function In2studyfinder() {
	function FilterHandling(dom) {

		var urlHandling = new window.In2studyfinder.UrlHandling(dom);
		var uiBehaviour = new window.In2studyfinder.UiBehaviour(dom);
		var start = new window.In2studyfinder.Start();
		var filterHandling = this;

		this.init = function() {
			filterHandling.resetAllFilterOptions();

			// "Alle" Checkbox unchecken wenn eine andere geklickt wird
			// und wieder checken, wenn keine mehr markiert ist
			$('.in2studyfinder-js-checkbox').change(function () {
				uiBehaviour.uncheckAllCheckbox($(this));
				filterHandling.filterChanged();
			});

			//alle Checkboxen unchecken, wenn "Alle" geklickt wird
			$('.in2studyfinder-js-checkbox-all').change(function () {
				uiBehaviour.uncheckOtherCheckboxes($(this));
				filterHandling.filterChanged();
			});
		};

		this.filterChanged = function(paginationPage) {
			var studyFinderForm = $('.in2studyfinder-js-filter');
			var url = urlHandling.removeUrlParam('cHash', studyFinderForm.attr('action'));
			if (paginationPage) {
				url += '&tx_in2studyfinder_studycourse%5B%40widget_0%5D%5BcurrentPage%5D=' + paginationPage;
			}
			$.ajax({
				type: "POST",
				url: url,
				data: studyFinderForm.serialize(),
				beforeSend: function () {
					uiBehaviour.enableLoading();
				},
				success: function (data) {
					urlHandling.saveSelectedOptionsToUrl(paginationPage);
					$('.in2studyfinder').html($(data).find('.in2studyfinder').html());
				},
				error: function () {
					//var lang = $('html').attr('lang');
					//if (lang === 'de') {
					//	alert('Ihre abgespeicherte Suche kann nicht mehr ausgeführt werden. Bitte wählen sie ihr Filter erneut');
					//} else {
					//	alert('This results page is not available any more. Please search again');
					//}
				},
				complete: function () {
					start.init();
					uiBehaviour.openPreviouslyOpenedFilterSections();
					uiBehaviour.disbaleLoading();
				},
				cache: false
			});
		};

		this.resetAllFilterOptions = function() {
			$('.in2studyfinder-js-reset-filter-options').on('click', function () {
				uiBehaviour.resetAllFilterCheckboxes();
				filterHandling.filterChanged();
			});
		}


	}

	// export to global scope
	if (!window.In2studyfinder) {
		window.In2studyfinder = {};
	}

	window.In2studyfinder.FilterHandling = FilterHandling;
})();
