/**
 * A utility for making AJAX calls.
 * @namespace AjaxUtility
 */
const AjaxUtility = {
  /**
   * Performs an AJAX call using the Fetch API.
   * @param {string} url - The URL for the request.
   * @param {Function} onStartCallback - The callback to execute when the request starts.
   * @param {Function} onSuccessCallback - The callback to execute on a successful response.
   */
  async ajaxCall(url, onStartCallback, onSuccessCallback) {
    onStartCallback();
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      onSuccessCallback(response);
    } catch (error) {
      console.error('Error during AJAX call:', error);
    }
  },
};

export default AjaxUtility;
