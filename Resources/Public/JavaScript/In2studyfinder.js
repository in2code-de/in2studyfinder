!function(){"use strict";var s=!0;window.In2studyfinder||(window.In2studyfinder={}),window.In2studyfinder.Start=function(){var w=$(".in2studyfinder");this.init=function(){if(0<w.length){var n=new window.In2studyfinder.Select(w),i=new window.In2studyfinder.FilterHandling(w),d=new window.In2studyfinder.UrlHandling(w),e=new window.In2studyfinder.UiBehaviour(w),t=new window.In2studyfinder.PaginationHandling(w);if(w.removeClass("no-js").addClass("js"),n.initializeSelect(),i.init(),t.init(),e.init(),s&&null!==document.querySelector(".in2studyfinder__view-list")){s=!1;var r=d.loadSelectedOptionsFromUrl();i.filterChanged(r)}}}}}(),jQuery(document).ready(function(){"use strict";(new window.In2studyfinder.Start).init()});