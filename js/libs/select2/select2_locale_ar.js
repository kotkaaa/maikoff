/**
 * Select2 Arabic translation.
 *
 * Author: Adel KEDJOUR <adel@kedjour.com>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['ar'] = {
        formatNoMatches: function () { return "Щ„Щ… ЩЉШЄЩ… Ш§Щ„Ш№Ш«Щ€Ш± Ш№Щ„Щ‰ Щ…Ш·Ш§ШЁЩ‚Ш§ШЄ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; if (n == 1){ return "Ш§Щ„Ш±Ш¬Ш§ШЎ ШҐШЇШ®Ш§Щ„ Ш­Ш±ЩЃ Щ€Ш§Ш­ШЇ Ш№Щ„Щ‰ Ш§Щ„ШЈЩѓШ«Ш±"; } return n == 2 ? "Ш§Щ„Ш±Ш¬Ш§ШЎ ШҐШЇШ®Ш§Щ„ Ш­Ш±ЩЃЩЉЩ† Ш№Щ„Щ‰ Ш§Щ„ШЈЩѓШ«Ш±" : "Ш§Щ„Ш±Ш¬Ш§ШЎ ШҐШЇШ®Ш§Щ„ " + n + " Ш№Щ„Щ‰ Ш§Щ„ШЈЩѓШ«Ш±"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; if (n == 1){ return "Ш§Щ„Ш±Ш¬Ш§ШЎ ШҐШЇШ®Ш§Щ„ Ш­Ш±ЩЃ Щ€Ш§Ш­ШЇ Ш№Щ„Щ‰ Ш§Щ„ШЈЩ‚Щ„"; } return n == 2 ? "Ш§Щ„Ш±Ш¬Ш§ШЎ ШҐШЇШ®Ш§Щ„ Ш­Ш±ЩЃЩЉЩ† Ш№Щ„Щ‰ Ш§Щ„ШЈЩ‚Щ„" : "Ш§Щ„Ш±Ш¬Ш§ШЎ ШҐШЇШ®Ш§Щ„ " + n + " Ш№Щ„Щ‰ Ш§Щ„ШЈЩ‚Щ„ "; },
        formatSelectionTooBig: function (limit) { if (limit == 1){ return "ЩЉЩ…ЩѓЩ†Щѓ ШЈЩ† ШЄШ®ШЄШ§Ш± ШҐШ®ШЄЩЉШ§Ш± Щ€Ш§Ш­ШЇ ЩЃЩ‚Ш·"; } return limit == 2 ? "ЩЉЩ…ЩѓЩ†Щѓ ШЈЩ† ШЄШ®ШЄШ§Ш± ШҐШ®ШЄЩЉШ§Ш±ЩЉЩ† ЩЃЩ‚Ш·" : "ЩЉЩ…ЩѓЩ†Щѓ ШЈЩ† ШЄШ®ШЄШ§Ш± " + limit + " ШҐШ®ШЄЩЉШ§Ш±Ш§ШЄ ЩЃЩ‚Ш·"; },
        formatLoadMore: function (pageNumber) { return "ШЄШ­Щ…ЩЉЩ„ Ш§Щ„Щ…ШІЩЉШЇ Щ…Щ† Ш§Щ„Щ†ШЄШ§Ш¦Ш¬вЂ¦"; },
        formatSearching: function () { return "Ш§Щ„ШЁШ­Ш«вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ar']);
})(jQuery);
