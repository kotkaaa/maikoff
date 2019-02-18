/**
 * Select2 Korean translation.
 * 
 * @author  Swen Mun <longfinfunnel@gmail.com>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['ko'] = {
        formatNoMatches: function () { return "кІ°кіј м—†мќЊ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "л„€л¬ґ м§§мЉµл‹€л‹¤. "+n+"кёЂмћђ лЌ” мћ…л Ґн•ґмЈјм„ёмљ”."; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "л„€л¬ґ к№Ѓл‹€л‹¤. "+n+"кёЂмћђ м§Ђм›ЊмЈјм„ёмљ”."; },
        formatSelectionTooBig: function (limit) { return "мµњлЊЂ "+limit+"к°њк№Њм§Ђл§Њ м„ нѓќн•м‹¤ м€ мћ€мЉµл‹€л‹¤."; },
        formatLoadMore: function (pageNumber) { return "л¶€лџ¬м¤лЉ” м¤‘вЂ¦"; },
        formatSearching: function () { return "кІЂмѓ‰ м¤‘вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ko']);
})(jQuery);
