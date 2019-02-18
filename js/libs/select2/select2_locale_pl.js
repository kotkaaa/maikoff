/**
 * Select2 Polish translation.
 *
 * @author  Jan Kondratowicz <jan@kondratowicz.pl>
 * @author  Uriy Efremochkin <efremochkin@uriy.me>
 * @author  MichaЕ‚ PoЕ‚tyn <mike@poltyn.com>
 * @author  Damian Zajkowski <damian.zajkowski@gmail.com>
 */
(function($) {
    "use strict";

    $.fn.select2.locales['pl'] = {
        formatNoMatches: function() {
            return "Brak wynikГіw";
        },
        formatInputTooShort: function(input, min) {
            return "Wpisz co najmniej" + character(min - input.length, "znak", "i");
        },
        formatInputTooLong: function(input, max) {
            return "Wpisana fraza jest za dЕ‚uga o" + character(input.length - max, "znak", "i");
        },
        formatSelectionTooBig: function(limit) {
            return "MoЕјesz zaznaczyД‡ najwyЕјej" + character(limit, "element", "y");
        },
        formatLoadMore: function(pageNumber) {
            return "ЕЃadowanie wynikГіwвЂ¦";
        },
        formatSearching: function() {
            return "SzukanieвЂ¦";
        }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['pl']);

    function character(n, word, pluralSuffix) {
        //Liczba pojedyncza - brak suffiksu
        //jeden znak
        //jeden element
        var suffix = '';
        if (n > 1 && n < 5) {
            //Liczaba mnoga iloЕ›Д‡ od 2 do 4 - wЕ‚asny suffiks
            //Dwa znaki, trzy znaki, cztery znaki.
            //Dwa elementy, trzy elementy, cztery elementy
            suffix = pluralSuffix;
        } else if (n == 0 || n >= 5) {
            //IloЕ›Д‡ 0 suffiks Гіw
            //Liczaba mnoga w iloЕ›ci 5 i wiД™cej - suffiks Гіw (nie poprawny dla wszystkich wyrazГіw, np. 100 wiadomoЕ›ci)
            //Zero znakГіw, PiД™Д‡ znakГіw, szeЕ›Д‡ znakГіw, siedem znakГіw, osiem znakГіw.
            //Zero elementГіw PiД™Д‡ elementГіw, szeЕ›Д‡ elementГіw, siedem elementГіw, osiem elementГіw.
            suffix = 'Гіw';
        }
        return " " + n + " " + word + suffix;
    }
})(jQuery);
