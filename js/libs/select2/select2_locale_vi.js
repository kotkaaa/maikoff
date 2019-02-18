/**
 * Select2 Vietnamese translation.
 * 
 * Author: Long Nguyen <olragon@gmail.com>, Nguyen Chien Cong
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['vi'] = {
        formatNoMatches: function () { return "KhГґng tГ¬m thбєҐy kбєїt quбєЈ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Vui lГІng nhбє­p nhiб»Ѓu hЖЎn " + n + " kГЅ tб»±"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Vui lГІng nhбє­p Г­t hЖЎn " + n + " kГЅ tб»±"; },
        formatSelectionTooBig: function (limit) { return "Chб»‰ cГі thб»ѓ chб»Ќn Д‘Ж°б»Јc " + limit + " lб»±a chб»Ќn"; },
        formatLoadMore: function (pageNumber) { return "Дђang lбєҐy thГЄm kбєїt quбєЈвЂ¦"; },
        formatSearching: function () { return "Дђang tГ¬mвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['vi']);
})(jQuery);

