/**
 * Select2 Persian translation.
 * 
 * Author: Ali Choopan <choopan@arsh.co>
 * Author: Ebrahim Byagowi <ebrahim@gnu.org>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['fa'] = {
        formatMatches: function (matches) { return matches + " Щ†ШЄЫЊШ¬Щ‡ Щ…Щ€Ш¬Щ€ШЇ Ш§ШіШЄШЊ Ъ©Щ„ЫЊШЇЩ‡Ш§ЫЊ Ш¬Щ‡ШЄ ШЁШ§Щ„Ш§ Щ€ ЩѕШ§ЫЊЫЊЩ† Ш±Ш§ ШЁШ±Ш§ЫЊ ЪЇШґШЄЩ† Ш§ШіШЄЩЃШ§ШЇЩ‡ Ъ©Щ†ЫЊШЇ."; },
        formatNoMatches: function () { return "Щ†ШЄЫЊШ¬Щ‡вЂЊШ§ЫЊ ЫЊШ§ЩЃШЄ Щ†ШґШЇ."; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Щ„Ш·ЩЃШ§Щ‹ " + n + " Щ†Щ€ЫЊШіЩ‡ ШЁЫЊШґШЄШ± Щ€Ш§Ш±ШЇ Щ†Щ…Ш§ЫЊЫЊШЇ"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Щ„Ш·ЩЃШ§Щ‹ " + n + " Щ†Щ€ЫЊШіЩ‡ Ш±Ш§ Ш­Ш°ЩЃ Ъ©Щ†ЫЊШЇ."; },
        formatSelectionTooBig: function (limit) { return "ШґЩ…Ш§ ЩЃЩ‚Ш· Щ…ЫЊвЂЊШЄЩ€Ш§Щ†ЫЊШЇ " + limit + " Щ…Щ€Ш±ШЇ Ш±Ш§ Ш§Щ†ШЄШ®Ш§ШЁ Ъ©Щ†ЫЊШЇ"; },
        formatLoadMore: function (pageNumber) { return "ШЇШ± Ш­Ш§Щ„ ШЁШ§Ш±ЪЇЫЊШ±ЫЊ Щ…Щ€Ш§Ш±ШЇ ШЁЫЊШґШЄШ±вЂ¦"; },
        formatSearching: function () { return "ШЇШ± Ш­Ш§Щ„ Ш¬ШіШЄШ¬Щ€вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['fa']);
})(jQuery);
