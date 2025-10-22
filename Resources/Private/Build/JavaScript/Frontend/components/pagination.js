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
      // Announce loading to screen readers
      this.announceToScreenReader('loading');

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

        // Announce results update to screen readers
        this.announceResultsUpdate();
      });
    }
  };

  /**
   * Announce loading or result changes to screen readers via aria-live region
   *
   * @param {string} messageType - Type of message: 'loading' or 'resultsUpdated'
   */
  announceToScreenReader(messageType) {
    const feedbackElement = this.studyfinderElement.querySelector('#filter-feedback');
    if (!feedbackElement) return;

    let message = '';
    const dataAttr = `data-${messageType.replace(/([A-Z])/g, '-$1').toLowerCase()}-text`;
    message = feedbackElement.getAttribute(dataAttr) || '';

    // Clear first to ensure change detection
    feedbackElement.textContent = '';

    // Use setTimeout to ensure the clear is processed before setting new text
    setTimeout(() => {
      feedbackElement.textContent = message;
    }, 100);
  }

  /**
   * Announce results update with current page info to screen readers
   */
  announceResultsUpdate() {
    const feedbackElement = this.studyfinderElement.querySelector('#filter-feedback');
    if (!feedbackElement) return;

    // Get the count from the updated DOM
    const itemCountElement = this.studyfinderElement.querySelector('.in2studyfinder__item-count p');

    if (itemCountElement) {
      const countText = itemCountElement.textContent.trim();
      const countMatch = countText.match(/^\d+/);

      if (countMatch) {
        const count = parseInt(countMatch[0], 10);
        let message = '';

        if (count === 1) {
          message = feedbackElement.getAttribute('data-results-count-single') || '';
        } else {
          const template = feedbackElement.getAttribute('data-results-count-multiple') || '';
          message = template.replace('%s', count);
        }

        // Clear first to ensure change detection
        feedbackElement.textContent = '';

        // Use setTimeout to ensure the clear is processed before setting new text
        setTimeout(() => {
          feedbackElement.textContent = message;
        }, 100);
      }
    }
  }

  onClick() {
  }
}

export {Pagination}
