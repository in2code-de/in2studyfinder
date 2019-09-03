define([], function() {
  'use strict';

  var UiUtility = {
    identifiers: {
      loader: '.in2studyfinder-loader',
      loaderActive: '.in2studyfinder-loader--active'
    }
  };

  /**
   * toggle class for element
   */
  UiUtility.toggleClassForElement = function(element, className) {
    if (element.classList.contains(className)) {
      element.classList.remove(className);
    } else {
      element.classList.add(className);
    }
  };

  /**
   * remove class from element
   *
   * @param element
   * @param className
   */
  UiUtility.removeClass = function(element, className) {
    if (element.classList.contains(className)) {
      element.classList.remove(className);
    }
  };

  /**
   * add class to element
   *
   * @param element
   * @param className
   */
  UiUtility.addClass = function(element, className) {
    if (!element.classList.contains(className)) {
      element.classList.add(className);
    }
  };

  /**
   * @param element
   * @return void
   */
  UiUtility.hideElement = function(element) {
    element.style.display = 'none';
  };

  /**
   * @param element
   * @return void
   */
  UiUtility.showElement = function(element) {
    element.style.display = 'inline-block';
  };

  /**
   * @param element
   * @return void
   */
  UiUtility.showElementAsBlock = function(element) {
    element.style.display = 'block';
  };

  /**
   * @param element
   * @return void
   */
  UiUtility.removeStyles = function(element) {
    element.removeAttribute('style');
  };

  /**
   *
   */
  UiUtility.enableLoader = function() {
    UiUtility.toggleClassForElement(
      document.querySelector(UiUtility.identifiers.loader),
      UiUtility.identifiers.loaderActive.substr(1)
    );
  };

  /**
   *
   */
  UiUtility.disableLoader = function() {
    if (document.querySelector(UiUtility.identifiers.loaderActive) !== null) {
      UiUtility.toggleClassForElement(
        document.querySelector(UiUtility.identifiers.loaderActive),
        UiUtility.identifiers.loaderActive.substr(1)
      );
    }
  };


  return UiUtility;
});
