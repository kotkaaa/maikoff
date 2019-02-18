/**
 * Select2 Japanese translation.
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['ja'] = {
        formatNoMatches: function () { return "и©ІеЅ“гЃЄгЃ—"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "еѕЊ" + n + "ж–‡е­—е…Ґг‚ЊгЃ¦гЃЏгЃ гЃ•гЃ„"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "ж¤њзґўж–‡е­—е€—гЃЊ" + n + "ж–‡е­—й•·гЃ™гЃЋгЃѕгЃ™"; },
        formatSelectionTooBig: function (limit) { return "жњЂе¤љгЃ§" + limit + "й …з›®гЃѕгЃ§гЃ—гЃ‹йЃёжЉћгЃ§гЃЌгЃѕгЃ›г‚“"; },
        formatLoadMore: function (pageNumber) { return "иЄ­иѕјдё­пЅҐпЅҐпЅҐ"; },
        formatSearching: function () { return "ж¤њзґўдё­пЅҐпЅҐпЅҐ"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ja']);
})(jQuery);
