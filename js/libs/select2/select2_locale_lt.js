/**
 * Select2 Lithuanian translation.
 * 
 * @author  CRONUS Karmalakas <cronus dot karmalakas at gmail dot com>
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['lt'] = {
        formatNoMatches: function () { return "AtitikmenЕі nerasta"; },
        formatInputTooShort: function (input, min) { return "Д®raЕЎykite dar" + character(min - input.length); },
        formatInputTooLong: function (input, max) { return "PaЕЎalinkite" + character(input.length - max); },
        formatSelectionTooBig: function (limit) {
        	return "JЕ«s galite pasirinkti tik " + limit + " element" + ((limit%100 > 9 && limit%100 < 21) || limit%10 == 0 ? "Еі" : limit%10 > 1 ? "us" : "Д…");
        },
        formatLoadMore: function (pageNumber) { return "Kraunama daugiau rezultatЕівЂ¦"; },
        formatSearching: function () { return "IeЕЎkomaвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['lt']);

    function character (n) {
        return " " + n + " simbol" + ((n%100 > 9 && n%100 < 21) || n%10 == 0 ? "iЕі" : n%10 > 1 ? "ius" : "ДЇ");
    }
})(jQuery);
