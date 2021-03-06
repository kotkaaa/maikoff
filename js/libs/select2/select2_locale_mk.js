/**
 * Select2 Macedonian translation.
 * 
 * Author: Marko Aleksic <psybaron@gmail.com>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['mk'] = {
        formatNoMatches: function () { return "РќРµРјР° РїСЂРѕРЅР°СРґРµРЅРѕ СЃРѕРІРїР°С“Р°СљР°"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Р’Рµ РјРѕР»РёРјРµ РІРЅРµСЃРµС‚Рµ СѓС€С‚Рµ " + n + " РєР°СЂР°РєС‚РµСЂ" + (n == 1 ? "" : "Рё"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Р’Рµ РјРѕР»РёРјРµ РІРЅРµСЃРµС‚Рµ " + n + " РїРѕРјР°Р»РєСѓ РєР°СЂР°РєС‚РµСЂ" + (n == 1? "" : "Рё"); },
        formatSelectionTooBig: function (limit) { return "РњРѕР¶РµС‚Рµ РґР° РёР·Р±РµСЂРµС‚Рµ СЃР°РјРѕ " + limit + " СЃС‚Р°РІРє" + (limit == 1 ? "Р°" : "Рё"); },
        formatLoadMore: function (pageNumber) { return "Р’С‡РёС‚СѓРІР°СљРµ СЂРµР·СѓР»С‚Р°С‚РёвЂ¦"; },
        formatSearching: function () { return "РџСЂРµР±Р°СЂСѓРІР°СљРµвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['mk']);
})(jQuery);