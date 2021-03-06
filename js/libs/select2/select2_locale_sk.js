/**
 * Select2 Slovak translation.
 *
 * Author: David Vallner <david@vallner.net>
 */
(function ($) {
    "use strict";
    // use text for the numbers 2 through 4
    var smallNumbers = {
        2: function(masc) { return (masc ? "dva" : "dve"); },
        3: function() { return "tri"; },
        4: function() { return "ЕЎtyri"; }
    };
    $.fn.select2.locales['sk'] = {
        formatNoMatches: function () { return "NenaЕЎli sa Еѕiadne poloЕѕky"; },
        formatInputTooShort: function (input, min) {
            var n = min - input.length;
            if (n == 1) {
                return "ProsГ­m, zadajte eЕЎte jeden znak";
            } else if (n <= 4) {
                return "ProsГ­m, zadajte eЕЎte ДЏalЕЎie "+smallNumbers[n](true)+" znaky";
            } else {
                return "ProsГ­m, zadajte eЕЎte ДЏalЕЎГ­ch "+n+" znakov";
            }
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            if (n == 1) {
                return "ProsГ­m, zadajte o jeden znak menej";
            } else if (n >= 2 && n <= 4) {
                return "ProsГ­m, zadajte o "+smallNumbers[n](true)+" znaky menej";
            } else {
                return "ProsГ­m, zadajte o "+n+" znakov menej";
            }
        },
        formatSelectionTooBig: function (limit) {
            if (limit == 1) {
                return "MГґЕѕete zvoliЕҐ len jednu poloЕѕku";
            } else if (limit >= 2 && limit <= 4) {
                return "MГґЕѕete zvoliЕҐ najviac "+smallNumbers[limit](false)+" poloЕѕky";
            } else {
                return "MГґЕѕete zvoliЕҐ najviac "+limit+" poloЕѕiek";
            }
        },
        formatLoadMore: function (pageNumber) { return "NaДЌГ­tavajГє sa ДЏalЕЎie vГЅsledkyвЂ¦"; },
        formatSearching: function () { return "VyhДѕadГЎvanieвЂ¦"; }
    };

	$.extend($.fn.select2.defaults, $.fn.select2.locales['sk']);
})(jQuery);
