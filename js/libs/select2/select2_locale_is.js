/**
 * Select2 Icelandic translation.
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['is'] = {
        formatNoMatches: function () { return "Ekkert fannst"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Vinsamlegast skrifiГ° " + n + " staf" + (n > 1 ? "i" : "") + " Г­ viГ°bГіt"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Vinsamlegast styttiГ° texta um " + n + " staf" + (n > 1 ? "i" : ""); },
        formatSelectionTooBig: function (limit) { return "ГћГє getur aГ°eins valiГ° " + limit + " atriГ°i"; },
        formatLoadMore: function (pageNumber) { return "SГ¦ki fleiri niГ°urstГ¶Г°urвЂ¦"; },
        formatSearching: function () { return "LeitaвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['is']);
})(jQuery);
