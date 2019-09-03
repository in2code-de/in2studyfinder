define(
  [
    'TYPO3/CMS/In2studyfinder/Utility/UrlUtility',
    'TYPO3/CMS/In2studyfinder/Utility/AjaxUtility',
    'TYPO3/CMS/In2studyfinder/Utility/UiUtility'
  ],
  function(UrlUtility, AjaxUtility, UiUtility) {
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
      var url = event.target.href;

      if (typeof url !== 'undefined') {
        var targetPage = UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[@widget_0][currentPage]');

        if (typeof targetPage !== 'undefined' && targetPage !== '') {
          AjaxUtility.ajaxCall(
            url,
            PaginationModule.onPaginationCallStart,
            PaginationModule.onPaginationCallSuccess
          );
        }
      }
    };

    /**
     * @return {void}
     */
    PaginationModule.onPaginationCallStart = function() {
      UiUtility.enableLoader();
    };

    /**
     * @param xhttp
     *
     * @return {void}
     */
    PaginationModule.onPaginationCallSuccess = function(xhttp) {
      var tempElement = document.createElement('div');
      tempElement.innerHTML = xhttp.responseText;

      document.querySelector(PaginationModule.identifiers.in2studyfinderContainer).parentNode.replaceChild(
        tempElement.querySelector(PaginationModule.identifiers.in2studyfinderContainer),
        document.querySelector(PaginationModule.identifiers.in2studyfinderContainer)
      );

      var Frontend = require("TYPO3/CMS/In2studyfinder/Frontend");
      Frontend.initialize();

      UiUtility.disableLoader();
    };

    return PaginationModule;
  }
);
