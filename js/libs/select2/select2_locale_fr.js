/**
 * Select2 French translation
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['fr'] = {
        formatMatches: function (matches) { return matches + " rГ©sultats sont disponibles, utilisez les flГЁches haut et bas pour naviguer."; },
        formatNoMatches: function () { return "Aucun rГ©sultat trouvГ©"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Saisissez " + n + " caractГЁre" + (n == 1? "" : "s") + " supplГ©mentaire" + (n == 1? "" : "s") ; },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Supprimez " + n + " caractГЁre" + (n == 1? "" : "s"); },
        formatSelectionTooBig: function (limit) { return "Vous pouvez seulement sГ©lectionner " + limit + " Г©lГ©ment" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "Chargement de rГ©sultats supplГ©mentairesвЂ¦"; },
        formatSearching: function () { return "Recherche en coursвЂ¦"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['fr']);
})(jQuery);
