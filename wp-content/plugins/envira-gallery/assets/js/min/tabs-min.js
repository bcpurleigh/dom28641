!function($){$(function(){var a=".envira-tabs-nav",t=window.location.hash,i=window.location.hash.replace("!","");if(t&&t.indexOf("envira-tab-")>=0){var e=$(i).parent(),n=$(e).data("navigation");$(n).find(".envira-active").removeClass("envira-active"),$(e).find(".envira-active").removeClass("envira-active"),$(n).find('a[href="'+i+'"]').addClass("envira-active"),$(i).addClass("envira-active");var r=$("#post").attr("action");r&&(r=r.split("#")[0],$("#post").attr("action",r+window.location.hash))}$(a).on("click",".nav-tab, a",function(t){t.preventDefault();var i=$(this).closest(a),e=$(i).data("container"),n=$(i).data("update-hashbang"),r="undefined"!=typeof $(this).attr("href")?$(this).attr("href"):$(this).data("tab");if(!$(this).hasClass("envira-active")&&("undefined"!=typeof $(this).attr("for")&&($("input#"+$(this).attr("for")).prop("checked")||$("input#"+$(this).attr("for")).prop("checked",!0).trigger("change")),$(i).find(".envira-active").removeClass("envira-active"),$(e).find(".envira-active").removeClass("envira-active"),$(this).addClass("envira-active"),$(e).find(r).addClass("envira-active"),"1"==n)){window.location.hash=r.split("#").join("#!");var s=$("#post").attr("action");s&&(s=s.split("#")[0],$("#post").attr("action",s+window.location.hash))}})})}(jQuery);