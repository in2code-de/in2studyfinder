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

    /**
     *
     * @param array
     * @param key
     * @param value
     * @returns {int} the array key of the object. -1 if no match is found
     */
    ArrayUtility.containsObjectWithKey = function(array, key, value) {
      if (Array.isArray(array)) {
        for (var i = 0; i <= array.length - 1; i++) {
          if (array[i] instanceof Object) {
            if (array[i].hasOwnProperty(key) && array[i][key] === value) {
              return i;
            }
          }
        }
      }
      return -1;
    };

    return ArrayUtility;
  }
)
;
