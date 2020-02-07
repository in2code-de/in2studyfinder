import {UrlUtility} from "../Utility/UrlUtility";

class PaginationModule {
  constructor() {
    this.identifiers = {
      in2studyfinderContainer: '.in2studyfinder',
      paginationLink: '.js-get-by-ajax',
      paginationContainer: '.js-in2studyfinder-pagebrowser',
      loader: '.in2studyfinder-loader',
      loaderActive: '.in2studyfinder-loader--active'
    };
  }

  /**
   * initialize function
   *
   * @return {void}
   */
  initialize() {
    if (document.querySelector(this.identifiers.paginationContainer) !== null) {
      var paginationList = document.querySelector(this.identifiers.paginationContainer);
      paginationList.addEventListener('click', this.callPagination);
    }
  };

  callPagination(event) {
    event.preventDefault();
    var targetPage = 1;
    var url = event.target.href;
    if (UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[@widget_0][currentPage]') !== '') {
      targetPage = UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[@widget_0][currentPage]');
    }

    UrlUtility.addOrUpdateHash('page', [targetPage]);

    //FilterModule.updateFilter(targetPage);
  };
}

export let paginationModule = new PaginationModule();
