import {Pagination} from "./pagination";
import {Filter} from "./filter";
import {Quickselect} from "./quickselect";

class Instance {

  constructor(element) {
    this.identifier = {
      paginationContainer: '.js-in2studyfinder-pagebrowser',
      filterContainer: '.js-in2studyfinder-filter',
      quickSelectContainer: '.js-in2studyfinder-select'
    };

    this.hasPagination = false;
    this.pagination = null;

    this.hasFilter = false;
    this.filter = null;

    this.hasQuickselect = false;
    this.quickselect = null;

    this.element = element;
  }

  init() {
    if (this.element.querySelector(this.identifier.paginationContainer) !== null) {
      this.hasPagination = true;
      this.pagination = new Pagination(this.element);
      this.pagination.init();
    }

    if (this.element.querySelector(this.identifier.filterContainer) !== null) {
      this.hasFilter = true;
      this.filter = new Filter(this.element);
      this.filter.init();
    }

    if (this.element.querySelector(this.identifier.quickSelectContainer) !== null) {
      this.hasQuickselect = true;
      this.quickselect = new Quickselect(this.element);
      this.quickselect.init();
    }
  }

  update(studyfinderElement) {
    this.element = studyfinderElement;

    if (this.element.querySelector(this.identifier.paginationContainer) !== null) {
      this.pagination.update(studyfinderElement);
    }

    if (this.element.querySelector(this.identifier.filterContainer) !== null) {
      this.filter.update(studyfinderElement);
    }

    if (this.element.querySelector(this.identifier.quickSelectContainer) !== null) {
      this.quickselect.update(studyfinderElement);
    }
  }

}

export {Instance}
