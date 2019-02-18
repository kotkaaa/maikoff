/**
 * Select2 Norwegian BokmГҐl translation.
 *
 * Author: Torgeir Veimo <torgeir.veimo@gmail.com>
 * Author: BjГёrn Johansen <post@bjornjohansen.no>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['nb'] = {
        formatMatches: function (matches) { if (matches === 1) { return "Ett resultat er tilgjengelig, trykk enter for ГҐ velge det."; } return matches + " resultater er tilgjengelig. Bruk piltastene opp og ned for ГҐ navigere."; },
        formatNoMatches: function () { return "Ingen treff"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Vennligst skriv inn " + n + (n>1 ? " flere tegn" : " tegn til"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Vennligst fjern " + n + " tegn"; },
        formatSelectionTooBig: function (limit) { return "Du kan velge maks " + limit + " elementer"; },
        formatLoadMore: function (pageNumber) { return "Laster flere resultater вЂ¦"; },
        formatSearching: function () { return "SГёker вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['no']);
})(jQuery);

