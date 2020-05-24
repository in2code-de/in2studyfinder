import {UiUtility} from "../Utility";

class Loader {
  constructor() {
    this.identifiers = {
      loader: '.in2studyfinder-loader',
      loaderActive: '.in2studyfinder-loader--active'
    }
  }

  /**
   *
   */
  enableLoader() {
    UiUtility.toggleClassForElement(
      document.querySelector(this.identifiers.loader),
      this.identifiers.loaderActive.substr(1)
    );
  };

  /**
   *
   */
  disableLoader() {
    if (document.querySelector(this.identifiers.loaderActive) !== null) {
      UiUtility.toggleClassForElement(
        document.querySelector(this.identifiers.loaderActive),
        this.identifiers.loaderActive.substr(1)
      );
    }
  };
}

export let loader = new Loader();
