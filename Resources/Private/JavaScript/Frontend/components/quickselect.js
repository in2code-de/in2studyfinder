import TomSelect from "tom-select";

class Quickselect {

  constructor(studyfinderContainer) {
    this.identifier = {
      quicksearchContainer: '.js-in2studyfinder-quick-search',
      select: '.js-in2studyfinder-select'
    };

    this.settings = {
      maxOptions: null,
      searchField: ['text', 'keywords'],
      onItemAdd: function(value, item) {
        let url = item.getAttribute('data-url');

        if (url.length) {
          window.location.href = url;
        }
      },
      render: {
        item: function(data, escape) {
          return '<div title="' + escape(data.text) + '" data-url="' + data.url + '">' + escape(data.text) + '</div>';
        },
        no_results: function(data, escape) {
          let lang = document.querySelector('html').getAttribute('lang');
          let message = 'No results found';
          if (lang === 'de' || 'de-DE') {
            message = 'Keine Ergebnisse gefunden';
          }

          return '<div class="no-results">' + message + '</div>';
        },
      }
    };
    this.tomSelect = null;

    this.in2studyfinderContainer = studyfinderContainer;
  }

  init() {
    this.tomSelect = new TomSelect(this.in2studyfinderContainer.querySelector(this.identifier.select), this.settings);
    this.tomSelect.clear();
  }

  update(studyfinderContainer) {
    this.in2studyfinderContainer = studyfinderContainer;
    if (this.tomSelect !== null) {
      this.tomSelect.destroy();
    }

    this.init();
  }
}

export {Quickselect}
