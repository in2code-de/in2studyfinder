define([], function() {
	'use strict';

	var AjaxUtility = {};

	/**
	 * ajaxCall
	 */
	AjaxUtility.ajaxCall = function(url, onStartCallback, onSuccessCallback) {
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
	};

	return AjaxUtility;
});
