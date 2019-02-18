/**
 * Select2 Traditional Chinese translation
 */
(function ($) {
    "use strict";
    $.fn.select2.locales['zh-TW'] = {
        formatNoMatches: function () { return "жІ’жњ‰ж‰ѕе€°з›ёз¬¦зљ„й …з›®"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "и«‹е†Ќијёе…Ґ" + n + "еЂ‹е­—е…ѓ";},
        formatInputTooLong: function (input, max) { var n = input.length - max; return "и«‹е€ЄжЋ‰" + n + "еЂ‹е­—е…ѓ";},
        formatSelectionTooBig: function (limit) { return "дЅ еЏЄиѓЅйЃёж“‡жњЂе¤љ" + limit + "й …"; },
        formatLoadMore: function (pageNumber) { return "иј‰е…Ґдё­вЂ¦"; },
        formatSearching: function () { return "жђње°‹дё­вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['zh-TW']);
})(jQuery);
