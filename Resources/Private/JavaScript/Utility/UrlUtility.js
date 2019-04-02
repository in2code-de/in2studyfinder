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

  UrlUtility.addHashToUrl = function(attribute, value) {
    var hash = window.location.hash;

    if (hash) {
      var split = hash.split('=');

      console.log(split);
    } else {
      hash = attribute + '=' + value;
    }


    window.location.hash = hash;
  };

  /**
   *
   * @param url
   * @param attribute
   * @param value
   * @returns {string|*}
   */
  UrlUtility.addAttributeToUrl = function(url, attribute, value) {

    var divider = '?';

    if (url.indexOf('?') !== -1) {
      divider = '&';
    }

    url += divider + attribute + '=' + value;

    return url;
  };

  /**
   * Serialize an given Form
   *
   * @param form
   * @returns {string}
   */
  UrlUtility.serialize = function(form) {
    var field, s = [];
    if (typeof form === 'object' && form.nodeName === 'FORM') {
      var len = form.elements.length;
      for (var i = 0; i < len; i++) {
        field = form.elements[i];
        if (field.name &&
          !field.disabled &&
          field.type !== 'file' &&
          field.type !== 'reset' &&
          field.type !== 'submit' &&
          field.type !== 'button'
        ) {
          if (field.type === 'select-multiple') {
            for (var j = form.elements[i].options.length - 1; j >= 0; j--) {
              if (field.options[j].selected) {
                s[s.length] = encodeURIComponent(field.name) + '=' + encodeURIComponent(field.options[j].value);
              }
            }
          } else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
            s[s.length] = encodeURIComponent(field.name) + '=' + encodeURIComponent(field.value);
          }
        }
      }
    }
    return s.join('&').replace(/%20/g, '+');
  };

  /**
   * @return array
   */
  UrlUtility.getHashArgumentsFromUrl = function() {
    if (window.location.hash) {
      var hash = window.location.hash.split('#')[1];
      var argumentArray = hash.split(/[&;]/g);
      var hashArguments = [];
      for (var i = 0; i < argumentArray.length; i++) {

        var singleArgument = argumentArray[i].split(/[=;]/g);
        var values = singleArgument[1].split(/[+;]/g);

        hashArguments[i] = {
          name: singleArgument[0],
          values: values
        };
      }

      return hashArguments;
    }

    return [];

  };

  return UrlUtility;
});
