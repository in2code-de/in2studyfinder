const UrlUtility = {
  /**
   * Removes a parameter from a given URL.
   * @param {string} urlString - The URL to modify.
   * @param {string} parameter - The name of the parameter to remove.
   * @returns {string} The modified URL.
   */
  removeParameterFromUrl(urlString, parameter) {
    try {
      const url = new URL(urlString);
      url.searchParams.delete(parameter);
      return url.toString();
    } catch (e) {
      // Handle cases where the urlString is not a full URL (e.g., /path?a=1)
      // This fallback handles relative URLs but is less robust than the URL API.
      const [path, params] = urlString.split('?');
      if (!params) {
        return urlString;
      }
      const searchParams = new URLSearchParams(params);
      searchParams.delete(parameter);
      const newParams = searchParams.toString();
      return newParams ? `${path}?${newParams}` : path;
    }
  },

  /**
   * Gets the value of a parameter from a URL.
   * @param {string} urlString - The URL to parse.
   * @param {string} parameter - The name of the parameter to find.
   * @returns {string|null} The value of the parameter or null if not found.
   */
  getParameterFromUrl(urlString, parameter) {
    try {
      const url = new URL(urlString);
      return url.searchParams.get(parameter);
    } catch (e) {
      // Fallback for relative URLs
      const params = urlString.split('?')[1] || '';
      return new URLSearchParams(params).get(parameter);
    }
  },

  /**
   * Adds a parameter and its value to a URL.
   * If the parameter already exists, its value is updated.
   * @param {string} urlString - The URL to modify.
   * @param {string} attribute - The name of the parameter to add.
   * @param {string} value - The value of the parameter.
   * @returns {string} The modified URL.
   */
  addAttributeToUrl(urlString, attribute, value) {
    try {
      const url = new URL(urlString);
      url.searchParams.set(attribute, value);
      return url.toString();
    } catch (e) {
      // Fallback for relative URLs
      const [path, params] = urlString.split('?');
      const searchParams = new URLSearchParams(params);
      searchParams.set(attribute, value);
      return `${path}?${searchParams.toString()}`;
    }
  },
};

export default UrlUtility;
