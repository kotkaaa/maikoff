/**
 * Select2 Azerbaijani translation.
 *
 * Author: Farhad Safarov <farhad.safarov@gmail.com>
 */
(function ($) {
    "use strict";

     $.fn.select2.locales['az'] = {
        formatMatches: function (matches) { return matches + " nЙ™ticЙ™ mГ¶vcuddur, hЙ™rЙ™kЙ™t etdirmЙ™k ГјГ§Гјn yuxarД± vЙ™ aЕџaДџД± dГјymЙ™lЙ™rindЙ™n istifadЙ™ edin."; },
        formatNoMatches: function () { return "NЙ™ticЙ™ tapД±lmadД±"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return n + " simvol daxil edin"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return n + " simvol silin"; },
        formatSelectionTooBig: function (limit) { return "SadЙ™cЙ™ " + limit + " element seГ§Й™ bilЙ™rsiniz"; },
        formatLoadMore: function (pageNumber) { return "Daha Г§ox nЙ™ticЙ™ yГјklЙ™nirвЂ¦"; },
        formatSearching: function () { return "AxtarД±lД±rвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['az']);
})(jQuery);
