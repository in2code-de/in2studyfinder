!function(){"use strict";window.In2studyfinder||(window.In2studyfinder={}),window.In2studyfinder.UrlHandling=function(i){this.removeUrlParam=function(i,n){var t=n.split("?")[0],e=[],o=-1!==n.indexOf("?")?n.split("?")[1]:"";if(""!==o){for(var a=(e=o.split("&")).length-1;0<=a;a-=1)e[a].split("=")[0]===i&&e.splice(a,1);t=t+"?"+e.join("&")}return t},this.saveSelectedOptionsToUrl=function(i){var n={},t="",e=$(".js-in2studyfinder-filter").find("input.in2studyfinder-js-checkbox:checked");$(e).each(function(){var i=$(this).closest("fieldset").data("filtergroup");void 0===n[i]&&(n[i]=[]),n[i].push($(this).val())}),$(n).each(function(i,n){$.each(n,function(i,n){t+=i+"--",$.each(n,function(i,n){t+=n+"+"}),t=t.replace(/\+$/,""),t+="__"}),t=t.replace(/__$/,"")}),i&&(t+="page="+i),window.location=location.protocol+"//"+location.host+location.pathname+(location.search?location.search:"")+"#"+t},this.loadSelectedOptionsFromUrl=function(){var i=window.location.hash.split("#");if(1 in i){var n;if(-1!==(i=i[1]).indexOf("page=")&&(n=i.split("page=")[1],i=i.split("page=")[0]),""!==i){var t=i.split("__");$(t).each(function(i,n){var t=n.split("--"),e=n.substr(0,n.indexOf("--")),o=t[1].split("+");$(o).each(function(i,n){$("#"+e+"_"+n).prop("checked",!0)})})}return n}}}}();