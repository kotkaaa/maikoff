/**
 * Select2 Bulgarian translation.
 * 
 * @author  Lubomir Vikev <lubomirvikev@gmail.com>
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['bg'] = {
        formatNoMatches: function () { return "РќСЏРјР° РЅР°РјРµСЂРµРЅРё СЃСЉРІРїР°РґРµРЅРёСЏ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "РњРѕР»СЏ РІСЉРІРµРґРµС‚Рµ РѕС‰Рµ " + n + " СЃРёРјРІРѕР»" + (n > 1 ? "Р°" : ""); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "РњРѕР»СЏ РІСЉРІРµРґРµС‚Рµ СЃ " + n + " РїРѕ-РјР°Р»РєРѕ СЃРёРјРІРѕР»" + (n > 1 ? "Р°" : ""); },
        formatSelectionTooBig: function (limit) { return "РњРѕР¶РµС‚Рµ РґР° РЅР°РїСЂР°РІРёС‚Рµ РґРѕ " + limit + (limit > 1 ? " РёР·Р±РѕСЂР°" : " РёР·Р±РѕСЂ"); },
        formatLoadMore: function (pageNumber) { return "Р—Р°СЂРµР¶РґР°С‚ СЃРµ РѕС‰РµвЂ¦"; },
        formatSearching: function () { return "РўСЉСЂСЃРµРЅРµвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['bg']);
})(jQuery);
