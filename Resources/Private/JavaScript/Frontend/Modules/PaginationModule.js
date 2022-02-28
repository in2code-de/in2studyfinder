import {UrlUtility} from "../Utility/UrlUtility";
import {filterModule} from "./FilterModule";


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
    if (UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[studyCoursesForPage][currentPage]') !== '') {
      targetPage = UrlUtility.getParameterFromUrl(url, 'tx_in2studyfinder_pi1[studyCoursesForPage][currentPage]');
    }


    UrlUtility.addOrUpdateHash('page', [targetPage]);

    filterModule.updateFilter(targetPage);
  };
}

export let paginationModule = new PaginationModule();
