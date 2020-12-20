class UiUtility {
  /**
   * toggle class for element
   */
  static toggleClassForElement(element, className) {

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
  static removeClass(element, className) {
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
  static addClass(element, className) {
    if (!element.classList.contains(className)) {
      element.classList.add(className);
    }
  };

  /**
   * @param element
   * @return void
   */
  static hideElement(element) {
    element.style.display = 'none';
  };

  /**
   * @param element
   * @return void
   */
  static showElement(element) {
    element.style.display = 'inline-block';
  };

  /**
   * @param element
   * @return void
   */
  static showElementAsBlock(element) {
    element.style.display = 'block';
  };

  /**
   * @param element
   * @return void
   */
  static removeStyles(element) {
    element.removeAttribute('style');
  };


}

export {UiUtility}
