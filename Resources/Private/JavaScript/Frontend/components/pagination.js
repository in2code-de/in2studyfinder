import {UrlUtility} from "../utility/urlUtility";
import {LoaderUtility} from "../utility/loaderUtility";

class Pagination {

  constructor(studyfinderElement) {
    this.identifier = {
      container: '.in2studyfinder',
      paginationContainer: '.js-in2studyfinder-pagebrowser',
      paginationLink: '.js-in2studyfinder-pagination-link',
    }

    this.studyfinderElement = studyfinderElement;
    this.paginationElement = studyfinderElement.querySelector(this.identifier.paginationContainer);
  }

  init() {
    this.paginationElement.querySelectorAll(this.identifier.paginationLink).forEach(function (item) {
      item.addEventListener('click', function (event) {
        this.onClick(event);
        this.call(event);
      }.bind(this));
    }.bind(this));
  }

  update(studyfinderElement) {
    this.studyfinderElement = studyfinderElement;
    this.paginationElement = studyfinderElement.querySelector(this.identifier.paginationContainer);

    this.paginationElement.querySelectorAll(this.identifier.paginationLink).forEach(function (item) {
      item.addEventListener('click', function (event) {
        this.onClick(event);
        this.call(event);
      }.bind(this));
    }.bind(this));
  }

  call(event) {
    event.preventDefault();

    let targetPage = event.target.getAttribute('data-target-page');
    let url = event.target.href;
    let instanceId = this.studyfinderElement.getAttribute('data-in2studyfinder-instance-id');

    UrlUtility.addOrUpdateHash('page', [targetPage]);

    if (window.in2studyfinder.getInstance(instanceId).hasFilter) {
      window.in2studyfinder.getInstance(instanceId).filter.call(targetPage);
    } else {
      LoaderUtility.enableLoader();

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
      }).then((response) => {
        return response.text();
      }).then((html) => {
        let tempElement = document.createElement('div');
        tempElement.innerHTML = html;
        this.studyfinderElement.innerHTML = tempElement.querySelector(this.identifier.container).innerHTML;

        LoaderUtility.disableLoader();
        window.in2studyfinder.getInstance(instanceId).update(this.studyfinderElement);
      });
    }
  };

  onClick() {
  }
}

export {Pagination}
