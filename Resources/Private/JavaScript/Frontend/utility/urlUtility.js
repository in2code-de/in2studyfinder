import {ArrayUtility} from "./arrayUtility";

class UrlUtility {
  /**
   * initialized all functions
   *
   * @return {void}
   */
  static removeParameterFromUrl(url, parameter) {
    let urlParts = url.split('?');
    if (urlParts.length >= 2) {

      let prefix = encodeURIComponent(parameter) + '=';
      let pars = urlParts[1].split(/[&;]/g);

      //reverse iteration as may be destructive
      for (let i = pars.length; i-- > 0;) {
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

  static getParameterFromUrl(url, parameter) {

    let parts = url.split('?'),
      value = '';

    if (parts.length >= 2) {

      let queryString = parts[1];
      queryString = '&' + queryString;

      let prefix = encodeURIComponent(parameter) + '=';
      let parameters = queryString.split(/[&;]/g);
      for (let i = parameters.length; i-- > 0;) {
        if (parameters[i].lastIndexOf(prefix, 0) !== -1) {
          value = parameters[i].split('=')[1];
          break;
        }
      }
    }

    return value;
  };

  /**
   *
   * @param {string} attribute
   * @param {array} values
   */
  static addOrUpdateHash(attribute, values) {
    let hashArguments = this.getHashArgumentsFromUrl();
    if (hashArguments.length > 0) {
      let key = ArrayUtility.containsObjectWithKey(hashArguments, 'name', attribute);
      if (key >= 0) {
        hashArguments[key]['values'] = values;
      }
    } else {
      hashArguments[0] = {name: attribute, values: values};
    }

    let hash = '#';
    // write hash
    for (let i = 0; i <= hashArguments.length - 1; i++) {
      if (i === hashArguments.length - 1) {
        hash += hashArguments[i].name + '=' + hashArguments[i].values.join();
      } else {
        hash += hashArguments[i].name + '=' + hashArguments[i].values.join() + '&';
      }
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
  static addAttributeToUrl(url, attribute, value) {

    let divider = '?';

    if (url.indexOf('?') !== -1) {
      divider = '&';
    }

    url += divider + attribute + '=' + value;

    return url;
  };

  /**
   * Serialize a given Form
   *
   * @param form
   * @returns {string}
   */
  static serialize(form) {
    let field, s = [];
    if (typeof form === 'object' && form.nodeName === 'FORM') {
      let len = form.elements.length;
      for (let i = 0; i < len; i++) {
        field = form.elements[i];
        if (field.name &&
          !field.disabled &&
          field.type !== 'file' &&
          field.type !== 'reset' &&
          field.type !== 'submit' &&
          field.type !== 'button'
        ) {
          if (field.type === 'select-multiple') {
            for (let j = form.elements[i].options.length - 1; j >= 0; j--) {
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
  static getHashArgumentsFromUrl() {
    if (window.location.hash) {
      let hash = window.location.hash.split('#')[1];
      let argumentArray = hash.split(/[&;]/g);
      let hashArguments = [];
      for (let i = 0; i < argumentArray.length; i++) {
        let singleArgument = argumentArray[i].split(/[=;]/g);

        if (singleArgument.length === 2) {
          let values = singleArgument[1].split(/[+;]/g);
          hashArguments[i] = {
            name: singleArgument[0],
            values: values
          };
        }
      }

      return hashArguments;
    }

    return [];

  };
}

export {UrlUtility}
