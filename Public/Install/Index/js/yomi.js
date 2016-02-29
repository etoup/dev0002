/**
 * 倒计时插件
 */
(function($){
    $.fn.yomi=function(){
        var data="";
        var _DOM=null;
        var TIMER;
        createdom =function(dom){
            _DOM=dom;
            data=$(dom).attr("data");
            data = data.replace(/-/g,"/");
            data = Math.round((new Date(data)).getTime()/1000);
            $(_DOM).append("<ul class='yomi'><li class='split'>投标倒计时:</li><li class='yomiday'></li><li class='split'>天</li><li class='yomihour'></li><li class='split'>:</li><li class='yomimin'></li><li class='split'>:</li><li class='yomisec'></li></ul>")
            reflash();

        };
        reflash=function(){
            var	range  	= data-Math.round((new Date()).getTime()/1000),
                secday = 86400, sechour = 3600,
                days 	= parseInt(range/secday),
                hours	= parseInt((range%secday)/sechour),
                min		= parseInt(((range%secday)%sechour)/60),
                sec		= ((range%secday)%sechour)%60;
            $(_DOM).find(".yomiday").html(nol(days));
            $(_DOM).find(".yomihour").html(nol(hours));
            $(_DOM).find(".yomimin").html(nol(min));
            $(_DOM).find(".yomisec").html(nol(sec));

        };
        TIMER = setInterval( reflash,1000 );
        nol = function(h){
            return h>9?h:'0'+h;
        }
        return this.each(function(){
            var $box = $(this);
            createdom($box);
        });
    }
})(jQuery);
$(function(){
    $(".yomibox").each(function(){
        $(this).yomi();
    });
    $("head").append("<style type='text/css'>.yomi {list-style:none;}.yomi li{float:left;background:#eee;color:#00a0e2;border-radius:3px;padding:8px;margin:0 10px;font-size:14px; font-weight:bold;}.yomi li.split{background:none;margin:0;padding:10px 0;color:#00a0e2;}</style>")
});