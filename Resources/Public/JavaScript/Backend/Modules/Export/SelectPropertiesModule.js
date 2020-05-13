define(["TYPO3/CMS/In2studyfinder/Backend/Utility/UiUtility"],function(r){"use strict";var s={selectedPropertiesList:document.querySelector(".js-in2studyfinder-selected-properties-list"),availablePropertiesList:document.querySelector(".js-in2studyfinder-property-list"),initialize:function(){s.addEventListener()},addEventListener:function(){document.querySelector(".js-in2studyfinder-property-list").addEventListener("click",s.addPropertyToSelectedProperties),document.querySelector(".js-in2studyfinder-remove-item").addEventListener("click",s.removeSelectedItems),document.querySelector(".js-in2studyfinder-move-item-up").addEventListener("click",s.moveSelectedItemsUp),document.querySelector(".js-in2studyfinder-move-item-down").addEventListener("click",s.moveSelectedItemsDown),document.querySelector(".js-in2studyfinder-move-item-end").addEventListener("click",s.moveSelectedItemsToEnd),document.querySelector(".js-in2studyfinder-move-item-begin").addEventListener("click",s.moveSelectedItemsToBegin)},addPropertyToSelectedProperties:function(e){var t,i=e.target;"true"===i.getAttribute("data-in2studyfinder-property-selectable")&&(t=i.cloneNode(!0),r.hideElement(i),r.removeStyles(t),t.getAttribute("data-in2studyfinder-parent-property-label")&&t.insertAdjacentHTML("afterbegin",t.getAttribute("data-in2studyfinder-parent-property-label")),s.selectedPropertiesList.add(t))},removeSelectedItems:function(){var e=s.getSelectionFromSelectedPropertiesList();if(e.length)for(var t=0;t<e.length;t++){var i=e[t];s.selectedPropertiesList.options[i.index].remove(),s.showPropertyOnAvailablePropertiesList(i.value)}},moveSelectedItemsUp:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i,r,n,o,d=0;d<e.length;d++){0<e[d].index&&(i=e[d].index-1,r=e[d].index,n=e[d],o=t.options[e[d].index-1],t.removeChild(n),t.removeChild(o),t.add(n,i),t.add(o,r))}},moveSelectedItemsDown:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i,r,n,o,d=e.length-1;0<=d;d--){e[d].index+1<=t.length-1&&(i=e[d].index,r=e[d].index+1,n=e[d],o=t.options[e[d].index+1],t.removeChild(o),t.removeChild(n),t.add(o,i),t.add(n,r))}},moveSelectedItemsToEnd:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i=e.length-1;0<=i;i--)t.removeChild(e[i]),t.add(e[i])},moveSelectedItemsToBegin:function(){var e=s.getSelectionFromSelectedPropertiesList(),t=s.selectedPropertiesList;if(e.length)for(var i=0;i<e.length;i++)t.removeChild(e[i]),t.add(e[i],0)},showPropertyOnAvailablePropertiesList:function(e){var t=s.availablePropertiesList.querySelector('option[value="'+e+'"]');r.showElementAsBlock(t)},getSelectionFromSelectedPropertiesList:function(){for(var e=[],t=s.selectedPropertiesList,i=0;i<s.selectedPropertiesList.length;i++)t.options[i].selected&&e.push(t.options[i]);return e}};return s});