define([], function() {
  'use strict';

  var ArrayUtility = {};

  /**
   *
   * @param value
   * @param array
   * @returns {boolean}
   */
  ArrayUtility.isInArray = function(value, array) {
    return array.indexOf(value) > -1;
  };

  return ArrayUtility;
});
