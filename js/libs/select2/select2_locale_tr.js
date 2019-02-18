/**
 * Select2 Turkish translation.
 * 
 * Author: Salim KAYABAЕћI <salim.kayabasi@gmail.com>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['tr'] = {
        formatMatches: function (matches) { if (matches === 1) { return "Sadece bir sonuГ§ bulundu, seГ§mek iГ§in enter tuЕџuna basabilirsiniz."; } return matches + " sonuГ§ bulundu, yukarД± ve aЕџaДџД± tuЕџlarД± ile seГ§ebilirsiniz."; },
        formatNoMatches: function () { return "SonuГ§ bulunamadД±"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "En az " + n + " karakter daha girmelisiniz"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return n + " karakter azaltmalД±sД±nД±z"; },
        formatSelectionTooBig: function (limit) { return "Sadece " + limit + " seГ§im yapabilirsiniz"; },
        formatLoadMore: function (pageNumber) { return "Daha fazlaвЂ¦"; },
        formatSearching: function () { return "AranД±yorвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['tr']);
})(jQuery);
