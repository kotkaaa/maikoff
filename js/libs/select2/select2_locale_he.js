/**
* Select2 Hebrew translation.
*
* Author: Yakir Sitbon <http://www.yakirs.net/>
*/
(function ($) {
    "use strict";

    $.fn.select2.locales['he'] = {
        formatNoMatches: function () { return "ЧњЧђ Ч ЧћЧ¦ЧђЧ• Ч”ЧЄЧђЧћЧ•ЧЄ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Ч Чђ ЧњЧ”Ч–Ч™Чџ ЧўЧ•Ч“ " + n + " ЧЄЧ•Ч•Ч™Чќ Ч Ч•ЧЎЧ¤Ч™Чќ"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Ч Чђ ЧњЧ”Ч–Ч™Чџ Ч¤Ч—Ч•ЧЄ " + n + " ЧЄЧ•Ч•Ч™Чќ"; },
        formatSelectionTooBig: function (limit) { return "Ч Ч™ЧЄЧџ ЧњЧ‘Ч—Ч•ЧЁ " + limit + " Ч¤ЧЁЧ™ЧЧ™Чќ"; },
        formatLoadMore: function (pageNumber) { return "ЧЧ•ЧўЧџ ЧЄЧ•Ч¦ЧђЧ•ЧЄ Ч Ч•ЧЎЧ¤Ч•ЧЄвЂ¦"; },
        formatSearching: function () { return "ЧћЧ—Ч¤Ч©вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['he']);
})(jQuery);
