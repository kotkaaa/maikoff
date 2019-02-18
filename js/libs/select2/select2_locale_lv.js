/**
 * Select2 Latvian translation.
 *
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['lv'] = {
        formatNoMatches: function () { return "SakritД«bu nav"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "LЕ«dzu ievadiet vД“l " + n + " simbol" + (n == 11 ? "us" : n%10 == 1 ? "u" : "us"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "LЕ«dzu ievadiet par " + n + " simbol" + (n == 11 ? "iem" : n%10 == 1 ? "u" : "iem") + " mazДЃk"; },
        formatSelectionTooBig: function (limit) { return "JЕ«s varat izvД“lД“ties ne vairДЃk kДЃ " + limit + " element" + (limit == 11 ? "us" : limit%10 == 1 ? "u" : "us"); },
        formatLoadMore: function (pageNumber) { return "Datu ielДЃdeвЂ¦"; },
        formatSearching: function () { return "MeklД“ЕЎanaвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['lv']);
})(jQuery);
