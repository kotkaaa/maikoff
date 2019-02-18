/**
 * Select2 Thai translation.
 *
 * Author: Atsawin Chaowanakritsanakul <joke@nakhon.net>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['th'] = {
        formatNoMatches: function () { return "а№„аёЎа№€аёћаёљаё‚а№‰аё­аёЎаё№аёҐ"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "а№‚аё›аёЈаё”аёћаёґаёЎаёћа№Ња№Ђаёћаёґа№€аёЎаё­аёµаёЃ " + n + " аё•аё±аё§аё­аё±аёЃаё©аёЈ"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "а№‚аё›аёЈаё”аёҐаёљаё­аё­аёЃ " + n + " аё•аё±аё§аё­аё±аёЃаё©аёЈ"; },
        formatSelectionTooBig: function (limit) { return "аё„аёёаё“аёЄаёІаёЎаёІаёЈаё–а№ЂаёҐаё·аё­аёЃа№„аё”а№‰а№„аёЎа№€а№ЂаёЃаёґаё™ " + limit + " аёЈаёІаёўаёЃаёІаёЈ"; },
        formatLoadMore: function (pageNumber) { return "аёЃаёіаёҐаё±аё‡аё„а№‰аё™аё‚а№‰аё­аёЎаё№аёҐа№Ђаёћаёґа№€аёЎвЂ¦"; },
        formatSearching: function () { return "аёЃаёіаёҐаё±аё‡аё„а№‰аё™аё‚а№‰аё­аёЎаё№аёҐвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['th']);
})(jQuery);
