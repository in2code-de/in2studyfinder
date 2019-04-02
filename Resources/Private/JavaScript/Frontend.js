define(
  [
    'TYPO3/CMS/In2studyfinder/Modules/Frontend/FilterModule',
    'TYPO3/CMS/In2studyfinder/Modules/Frontend/SelectModule',
    'TYPO3/CMS/In2studyfinder/Modules/Frontend/PaginationModule',
    'TYPO3/CMS/In2studyfinder/Utility/UiUtility'
  ],
  function(FilterModule, SelectModule, PaginationModule, UiUtility) {
    'use strict';

    var Frontend = {
      identifiers: {
        in2studyfinderContainer: '.in2studyfinder'
      }
    };

    Frontend.initialize = function() {
      var in2studyfinderContainer = document.querySelector(Frontend.identifiers.in2studyfinderContainer);

      if (in2studyfinderContainer !== null) {
        UiUtility.removeClass(in2studyfinderContainer, 'no-js');
        UiUtility.addClass(in2studyfinderContainer, 'js');
        FilterModule.initialize();
        SelectModule.initialize();
        PaginationModule.initialize();
      }
    };

    return Frontend;
  }
);
