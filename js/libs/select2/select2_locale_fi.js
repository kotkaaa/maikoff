/**
 * Select2 Finnish translation
 */
(function ($) {
    "use strict";
    $.fn.select2.locales['fi'] = {
        formatNoMatches: function () {
            return "Ei tuloksia";
        },
        formatInputTooShort: function (input, min) {
            var n = min - input.length;
            return "Ole hyvГ¤ ja anna " + n + " merkkiГ¤ lisГ¤Г¤";
        },
        formatInputTooLong: function (input, max) {
            var n = input.length - max;
            return "Ole hyvГ¤ ja anna " + n + " merkkiГ¤ vГ¤hemmГ¤n";
        },
        formatSelectionTooBig: function (limit) {
            return "Voit valita ainoastaan " + limit + " kpl";
        },
        formatLoadMore: function (pageNumber) {
            return "Ladataan lisГ¤Г¤ tuloksiaвЂ¦";
        },
        formatSearching: function () {
            return "EtsitГ¤Г¤nвЂ¦";
        }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['fi']);
})(jQuery);
