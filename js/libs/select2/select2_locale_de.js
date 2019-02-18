/**
 * Select2 German translation
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['de'] = {
        formatNoMatches: function () { return "Keine Гњbereinstimmungen gefunden"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Bitte " + n + " Zeichen mehr eingeben"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Bitte " + n + " Zeichen weniger eingeben"; },
        formatSelectionTooBig: function (limit) { return "Sie kГ¶nnen nur " + limit + " Eintr" + (limit === 1 ? "ag" : "Г¤ge") + " auswГ¤hlen"; },
        formatLoadMore: function (pageNumber) { return "Lade mehr ErgebnisseвЂ¦"; },
        formatSearching: function () { return "SucheвЂ¦"; },
        formatMatches: function (matches) { return matches + " Ergebnis " + (matches > 1 ? "se" : "") + " verfГјgbar, zum Navigieren die Hoch-/Runter-Pfeiltasten verwenden."; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['de']);
})(jQuery);