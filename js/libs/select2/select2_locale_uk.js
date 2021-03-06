/**
 * Select2 Ukrainian translation.
 * 
 * @author  bigmihail <bigmihail@bigmir.net>
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['uk'] = {
        formatMatches: function (matches) { return character(matches, "СЂРµР·СѓР»СЊС‚Р°С‚") + " Р·РЅР°Р№РґРµРЅРѕ, РІРёРєРѕСЂРёСЃС‚РѕРІСѓР№С‚Рµ РєР»Р°РІС–С€С– Р·С– СЃС‚СЂС–Р»РєР°РјРё РІРІРµСЂС… С‚Р° РІРЅРёР· РґР»СЏ РЅР°РІС–РіР°С†С–С—."; },
        formatNoMatches: function () { return "РќС–С‡РѕРіРѕ РЅРµ Р·РЅР°Р№РґРµРЅРѕ"; },
        formatInputTooShort: function (input, min) { return "Р’РІРµРґС–С‚СЊ Р±СѓР»СЊ Р»Р°СЃРєР° С‰Рµ " + character(min - input.length, "СЃРёРјРІРѕР»"); },
        formatInputTooLong: function (input, max) { return "Р’РІРµРґС–С‚СЊ Р±СѓР»СЊ Р»Р°СЃРєР° РЅР° " + character(input.length - max, "СЃРёРјРІРѕР»") + " РјРµРЅС€Рµ"; },
        formatSelectionTooBig: function (limit) { return "Р’Рё РјРѕР¶РµС‚Рµ РІРёР±СЂР°С‚Рё Р»РёС€Рµ " + character(limit, "РµР»РµРјРµРЅС‚"); },
        formatLoadMore: function (pageNumber) { return "Р—Р°РІР°РЅС‚Р°Р¶РµРЅРЅСЏ РґР°РЅРёС…вЂ¦"; },
        formatSearching: function () { return "РџРѕС€СѓРєвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['uk']);

    function character (n, word) {
        return n + " " + word + (n%10 < 5 && n%10 > 0 && (n%100 < 5 || n%100 > 19) ? n%10 > 1 ? "Рё" : "" : "С–РІ");
    }
})(jQuery);
