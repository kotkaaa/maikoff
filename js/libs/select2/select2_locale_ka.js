/**
 * Select2 Georgian (Kartuli) translation.
 * 
 * Author: Dimitri Kurashvili dimakura@gmail.com
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['ka'] = {
        formatNoMatches: function () { return "бѓ•бѓ”бѓ  бѓ›бѓќбѓбѓ«бѓ”бѓ‘бѓњбѓђ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "бѓ’бѓ—бѓ®бѓќбѓ•бѓ— бѓЁбѓ”бѓбѓ§бѓ•бѓђбѓњбѓќбѓ— бѓ™бѓбѓ“бѓ”бѓ• " + n + " бѓЎбѓбѓ›бѓ‘бѓќбѓљбѓќ"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "бѓ’бѓ—бѓ®бѓќбѓ•бѓ— бѓ¬бѓђбѓЁбѓђбѓљбѓќбѓ— " + n + " бѓЎбѓбѓ›бѓ‘бѓќбѓљбѓќ"; },
        formatSelectionTooBig: function (limit) { return "бѓ—бѓҐбѓ•бѓ”бѓњ бѓЁбѓ”бѓ’бѓбѓ«бѓљбѓбѓђбѓ— бѓ›бѓ®бѓќбѓљбѓќбѓ“ " + limit + " бѓ©бѓђбѓњбѓђбѓ¬бѓ”бѓ бѓбѓЎ бѓ›бѓќбѓњбѓбѓЁбѓ•бѓњбѓђ"; },
        formatLoadMore: function (pageNumber) { return "бѓЁбѓ”бѓ“бѓ”бѓ’бѓбѓЎ бѓ©бѓђбѓўбѓ•бѓбѓ бѓ—бѓ•бѓђвЂ¦"; },
        formatSearching: function () { return "бѓ«бѓ”бѓ‘бѓњбѓђвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ka']);
})(jQuery);
