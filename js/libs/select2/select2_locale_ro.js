/**
 * Select2 Romanian translation.
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['ro'] = {
        formatNoMatches: function () { return "Nu a fost gДѓsit nimic"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "VДѓ rugДѓm sДѓ introduceИ›i incДѓ " + n + " caracter" + (n == 1 ? "" : "e"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "VДѓ rugДѓm sДѓ introduceИ›i mai puИ›in de " + n + " caracter" + (n == 1? "" : "e"); },
        formatSelectionTooBig: function (limit) { return "AveИ›i voie sДѓ selectaИ›i cel mult " + limit + " element" + (limit == 1 ? "" : "e"); },
        formatLoadMore: function (pageNumber) { return "Se Г®ncarcДѓвЂ¦"; },
        formatSearching: function () { return "CДѓutareвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ro']);
})(jQuery);
