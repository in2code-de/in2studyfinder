define([], function() {
  'use strict';

  var MiscUtility = {};

  MiscUtility.getParentByClassName = function(element, className) {
    while ((element = element.parentElement) && !element.classList.contains(className));
    return element;
  }

  return MiscUtility;
});


