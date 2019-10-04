define(
  [
    'TYPO3/CMS/In2studyfinder/Utility/UrlUtility',
    'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility',
    'TYPO3/CMS/In2studyfinder/Utility/UiUtility',
    'TYPO3/CMS/In2studyfinder/Modules/Frontend/FilterModule',
  ],
  function(UrlUtility, AjaxUtility, UiUtility, FilterModule) {
    'use strict';

    var PaginationModule = {
      identifiers: {
        in2studyfinderContainer: '.in2studyfinder',
        paginationLink: '.js-get-by-ajax',
        paginationContainer: '.js-in2studyfinder-pagebrowser',
        loader: '.in2studyfinder-loader',
        loaderActive: '.in2studyfinder-loader--active'
      }
    };

    /**
     * initialize function
     *
     * @return {void}
     */
    PaginationModule.initialize = function() {
      if (document.querySelector(PaginationModule.identifiers.paginationContainer) !== null) {
        var paginationList = document.querySelector(PaginationModule.identifiers.paginationContainer);
        paginationList.addEventListener('click', PaginationModule.callPagination);
      }
    };

    PaginationModule.callPagination = function(event) {
      event.preventDefault();
      var targetPage = 1;
      var url = event.target.href;
      if (UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[@widget_0][currentPage]') !== '') {
        targetPage = UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[@widget_0][currentPage]');
      }

      UrlUtility.addOrUpdateHash('page', [targetPage]);

      FilterModule.updateFilter(targetPage);
    };

    return PaginationModule;
  }
);
