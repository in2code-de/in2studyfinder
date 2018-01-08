define([], function() {
	'use strict';

	var UrlUtility = {};

	/**
	 * initialized all functions
	 *
	 * @return {void}
	 */
	UrlUtility.removeParameterFromUrl = function(url, parameter) {
		var urlParts = url.split('?');
		if (urlParts.length >= 2) {

			var prefix = encodeURIComponent(parameter) + '=';
			var pars = urlParts[1].split(/[&;]/g);

			//reverse iteration as may be destructive
			for (var i = pars.length; i-- > 0;) {
				//idiom for string.startsWith
				if (pars[i].lastIndexOf(prefix, 0) !== -1) {
					pars.splice(i, 1);
				}
			}

			url = urlParts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
			return url;
		} else {
			return url;
		}
	};

	UrlUtility.getParameterFromUrl = function(url, parameter) {

		var parts = url.split('?'),
			value = '';

		if (parts.length >= 2) {

			var queryString = parts[1];
			queryString = '&' + queryString;

			var prefix = encodeURIComponent(parameter) + '=';
			var parameters = queryString.split(/[&;]/g);
			for (var i = parameters.length; i-- > 0;) {
				if (parameters[i].lastIndexOf(prefix, 0) !== -1) {
					value = parameters[i].split('=')[1];
					break;
				}
			}
		}

		return value;
	};

	return UrlUtility;
});
