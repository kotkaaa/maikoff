/**
 * Select2 Chinese translation
 */
(function ($) {
    "use strict";
    $.fn.select2.locales['zh-CN'] = {
        formatNoMatches: function () { return "жІЎжњ‰ж‰ѕе€°еЊ№й…ЌйЎ№"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "иЇ·е†Ќиѕ“е…Ґ" + n + "дёЄе­—з¬¦";},
        formatInputTooLong: function (input, max) { var n = input.length - max; return "иЇ·е€ жЋ‰" + n + "дёЄе­—з¬¦";},
        formatSelectionTooBig: function (limit) { return "дЅ еЏЄиѓЅйЂ‰ж‹©жњЂе¤љ" + limit + "йЎ№"; },
        formatLoadMore: function (pageNumber) { return "еЉ иЅЅз»“жћњдё­вЂ¦"; },
        formatSearching: function () { return "жђњзґўдё­вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['zh-CN']);
})(jQuery);
