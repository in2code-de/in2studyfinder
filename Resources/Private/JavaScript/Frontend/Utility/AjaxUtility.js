class AjaxUtility {
  /**
   * @param url
   * @param onStartCallback
   * @param onSuccessCallback
   */
  static ajaxCall(url, onStartCallback, onSuccessCallback) {
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {

      if (this.readyState === 1) {
        onStartCallback();
      }

      if (this.readyState === 4 && this.status === 200) {
        onSuccessCallback(this);
      }
    };
    xhttp.open('GET', url, true);
    xhttp.send();
  }
}

export {AjaxUtility}
