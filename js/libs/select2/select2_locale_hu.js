/**
 * Select2 Hungarian translation
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['hu'] = {
        formatNoMatches: function () { return "Nincs talГЎlat."; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "TГєl rГ¶vid. MГ©g " + n + " karakter hiГЎnyzik."; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "TГєl hosszГє. " + n + " karakterrel tГ¶bb, mint kellene."; },
        formatSelectionTooBig: function (limit) { return "Csak " + limit + " elemet lehet kivГЎlasztani."; },
        formatLoadMore: function (pageNumber) { return "TГ¶ltГ©sвЂ¦"; },
        formatSearching: function () { return "KeresГ©sвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['hu']);
})(jQuery);
