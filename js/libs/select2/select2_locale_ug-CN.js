/**
 * Select2 Uyghur translation
 */
(function ($) {
    "use strict";
    $.fn.select2.locales['ug-CN'] = {
        formatNoMatches: function () { return "Щ…Ш§Ші ЩѓЫђЩ„Щ‰ШЇЩ‰ШєШ§Щ† Ш¦Ы‡Ъ†Ы‡Ш± ШЄЫђЩѕЩ‰Щ„Щ…Щ‰ШЇЩ‰"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "ЩЉЫ•Щ†Ы• " + n + " ЪѕЫ•Ш±Щѕ ЩѓЩ‰Ш±ЪЇЫ€ШІЫ€Ъ­";},
        formatInputTooLong: function (input, max) { var n = input.length - max; return "" + n + "ЪѕЫ•Ш±Щѕ Ш¦Ы†Ъ†Ы€Ш±Ы€Ъ­";},
        formatSelectionTooBig: function (limit) { return "Ш¦Ы•Ъ­ ЩѓЫ†Щѕ ШЁЩ€Щ„ШєШ§Щ†ШЇШ§" + limit + " ШЄШ§Щ„ Ш¦Ы‡Ъ†Ы‡Ш± ШЄШ§Щ„Щ„Щ‰ЩЉШ§Щ„Ш§ЩЉШіЩ‰ШІ"; },
        formatLoadMore: function (pageNumber) { return "Ш¦Ы‡Ъ†Ы‡Ш±Щ„Ш§Ш± Ш¦Щ€Щ‚Ы‡Щ„Щ‰Ы‹Ш§ШЄЩ‰ШЇЫ‡вЂ¦"; },
        formatSearching: function () { return "Ш¦Щ‰ШІШЇЫ•Ы‹Ш§ШЄЩ‰ШЇЫ‡вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ug-CN']);
})(jQuery);
