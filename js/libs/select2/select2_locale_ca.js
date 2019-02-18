/**
 * Select2 Catalan translation.
 * 
 * Author: David Planella <david.planella@gmail.com>
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['ca'] = {
        formatNoMatches: function () { return "No s'ha trobat cap coincidГЁncia"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "IntroduГЇu " + n + " carГ cter" + (n == 1 ? "" : "s") + " mГ©s"; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "IntroduГЇu " + n + " carГ cter" + (n == 1? "" : "s") + "menys"; },
        formatSelectionTooBig: function (limit) { return "NomГ©s podeu seleccionar " + limit + " element" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "S'estan carregant mГ©s resultatsвЂ¦"; },
        formatSearching: function () { return "S'estГ  cercantвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['ca']);
})(jQuery);
