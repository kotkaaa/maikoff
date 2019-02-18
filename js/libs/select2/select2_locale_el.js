/**
 * Select2 Greek translation.
 * 
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['el'] = {
        formatNoMatches: function () { return "О”ОµОЅ ОІПЃО­ОёО·ОєО±ОЅ О±ПЂОїП„ОµО»О­ПѓОјО±П„О±"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "О О±ПЃО±ОєО±О»ОїПЌОјОµ ОµО№ПѓО¬ОіОµП„Оµ " + n + " ПЂОµПЃО№ПѓПѓПЊП„ОµПЃОї" + (n > 1 ? "П…П‚" : "") + " П‡О±ПЃО±ОєП„О®ПЃ" + (n > 1 ? "ОµП‚" : "О±"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "О О±ПЃО±ОєО±О»ОїПЌОјОµ ОґО№О±ОіПЃО¬П€П„Оµ " + n + " П‡О±ПЃО±ОєП„О®ПЃ" + (n > 1 ? "ОµП‚" : "О±"); },
        formatSelectionTooBig: function (limit) { return "ОњПЂОїПЃОµОЇП„Оµ ОЅО± ОµПЂО№О»О­ОѕОµП„Оµ ОјПЊОЅОї " + limit + " О±ОЅП„О№ОєОµОЇОјОµОЅ" + (limit > 1 ? "О±" : "Ої"); },
        formatLoadMore: function (pageNumber) { return "О¦ПЊПЃП„П‰ПѓО· ПЂОµПЃО№ПѓПѓПЊП„ОµПЃП‰ОЅвЂ¦"; },
        formatSearching: function () { return "О‘ОЅО±О¶О®П„О·ПѓО·вЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['el']);
})(jQuery);