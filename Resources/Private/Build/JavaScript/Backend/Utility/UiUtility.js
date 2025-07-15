const UiUtility = {
  /**
   * Toggles a class for a given element.
   * @param {HTMLElement} element - The DOM element.
   * @param {string} className - The class name to toggle.
   */
  toggleClassForElement(element, className) {
    element.classList.toggle(className);
  },

  /**
   * Hides an element by setting its display style to 'none'.
   * @param {HTMLElement} element - The DOM element to hide.
   */
  hideElement(element) {
    element.style.display = 'none';
  },

  /**
   * Shows an element by setting its display style to 'inline-block'.
   * @param {HTMLElement} element - The DOM element to show.
   */
  showElement(element) {
    element.style.display = 'inline-block';
  },

  /**
   * Shows an element by setting its display style to 'block'.
   * @param {HTMLElement} element - The DOM element to show.
   */
  showElementAsBlock(element) {
    element.style.display = 'block';
  },

  /**
   * Removes all inline styles from an element.
   * @param {HTMLElement} element - The DOM element.
   */
  removeStyles(element) {
    element.removeAttribute('style');
  },
};

export default UiUtility;
