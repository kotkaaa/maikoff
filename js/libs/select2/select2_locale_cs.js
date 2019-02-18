/**
 * Select2 Czech translation.
 * 
 * Author: Michal Marek <ahoj@michal-marek.cz>
 * Author - sklonovani: David Vallner <david@vallner.net>
 */
(function ($) {
    "use strict";
    // use text for the numbers 2 through 4
    var smallNumbers = {
        2: function(masc) { return (masc ? "dva" : "dvД›"); },
        3: function() { return "tЕ™i"; },
        4: function() { return "ДЌtyЕ™i"; }
    }
    $.fn.select2.locales['cs'] = {
        formatNoMatches: function () { return "Nenalezeny ЕѕГЎdnГ© poloЕѕky"; },
        formatInputTooShort: function (input, min) {
            var n = min - input.length;
            if (n == 1) {
                return "ProsГ­m zadejte jeЕЎtД› jeden znak";
            } else if (n <= 4) {
                return "ProsГ­m zadejte jeЕЎtД› dalЕЎГ­ "+smallNumbers[n](true)+" znaky";
            } else {
                return "ProsГ­m zadejte jeЕЎtД› dalЕЎГ­ch "+n+" znakЕЇ";
            }
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            if (n == 1) {
                return "ProsГ­m zadejte o jeden znak mГ©nД›";
            } else if (n <= 4) {
                return "ProsГ­m zadejte o "+smallNumbers[n](true)+" znaky mГ©nД›";
            } else {
                return "ProsГ­m zadejte o "+n+" znakЕЇ mГ©nД›";
            }
        },
        formatSelectionTooBig: function (limit) {
            if (limit == 1) {
                return "MЕЇЕѕete zvolit jen jednu poloЕѕku";
            } else if (limit <= 4) {
                return "MЕЇЕѕete zvolit maximГЎlnД› "+smallNumbers[limit](false)+" poloЕѕky";
            } else {
                return "MЕЇЕѕete zvolit maximГЎlnД› "+limit+" poloЕѕek";
            }
        },
        formatLoadMore: function (pageNumber) { return "NaДЌГ­tajГ­ se dalЕЎГ­ vГЅsledkyвЂ¦"; },
        formatSearching: function () { return "VyhledГЎvГЎnГ­вЂ¦"; }
    };

	$.extend($.fn.select2.defaults, $.fn.select2.locales['cs']);
})(jQuery);
