define(["TYPO3/CMS/In2studyfinder/Utility/UiUtility"],function(r){"use strict";var s={selectedPropertiesList:document.querySelector(".js-in2studyfinder-selected-properties-list"),availablePropertiesList:document.querySelector(".js-in2studyfinder-property-list"),initialize:function(){s.addEventListener()},addEventListener:function(){document.querySelector(".js-in2studyfinder-property-list").addEventListener("click",s.addPropertyToSelectedProperties),document.querySelector(".js-in2studyfinder-remove-item").addEventListener("click",s.removeSelectedItems),document.querySelector(".js-in2studyfinder-move-item-up").addEventListener("click",s.moveSelectedItemsUp),document.querySelector(".js-in2studyfinder-move-item-down").addEventListener("click",s.moveSelectedItemsDown),document.querySelector(".js-in2studyfinder-move-item-end").addEventListener("click",s.moveSelectedItemsToEnd),document.querySelector(".js-in2studyfinder-move-item-begin").addEventListener("click",s.moveSelectedItemsToBegin)},addPropertyToSelectedProperties:function(e){var t=e.target;if("true"===t.getAttribute("data-in2studyfinder-property-selectable")){var i=t.cloneNode(!0);r.hideElement(t),r.removeStyles(i),i.getAttribute("data-in2studyfinder-parent-property-label")&&i.insertAdjacentHTML("afterbegin",i.getAttribute("data-in2studyfinder-parent-property-label")),s.selectedPropertiesList.add(i)}},removeSelectedItems:function(){var e=s.getSelectionFromSelectedPropertiesList();if(e.length)for(var t=0;t<e.length;t++){var i=e[t];s.selectedPropertiesList.options[i.index].remove(),s.showPropertyOnAvailablePropertiesList(i.value)}},moveSelectedItemsUp:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i=0;i<e.length;i++)if(0<e[i].index){var r=e[i].index-1,o=e[i].index,n=e[i],d=t.options[e[i].index-1];t.removeChild(n),t.removeChild(d),t.add(n,r),t.add(d,o)}},moveSelectedItemsDown:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i=e.length-1;0<=i;i--)if(e[i].index+1<=t.length-1){var r=e[i].index,o=e[i].index+1,n=e[i],d=t.options[e[i].index+1];t.removeChild(d),t.removeChild(n),t.add(d,r),t.add(n,o)}},moveSelectedItemsToEnd:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i=e.length-1;0<=i;i--)t.removeChild(e[i]),t.add(e[i])},moveSelectedItemsToBegin:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i=0;i<e.length;i++)t.removeChild(e[i]),t.add(e[i],0)},showPropertyOnAvailablePropertiesList:function(e){var t=s.availablePropertiesList.querySelector('option[value="'+e+'"]');r.showElementAsBlock(t)},getSelectionFromSelectedPropertiesList:function(){for(var e=[],t=s.selectedPropertiesList,i=0;i<s.selectedPropertiesList.length;i++)t.options[i].selected&&e.push(t.options[i]);return e}};return s});