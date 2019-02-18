/* Canadian-French initialisation for the jQuery UI date picker plugin. */
jQuery(function ($) {
	$.datepicker.regional['fr-CA'] = {
		closeText: 'Fermer',
		prevText: 'PrГ©cГ©dent',
		nextText: 'Suivant',
		currentText: 'Aujourd\'hui',
		monthNames: ['janvier', 'fГ©vrier', 'mars', 'avril', 'mai', 'juin',
			'juillet', 'aoГ»t', 'septembre', 'octobre', 'novembre', 'dГ©cembre'],
		monthNamesShort: ['janv.', 'fГ©vr.', 'mars', 'avril', 'mai', 'juin',
			'juil.', 'aoГ»t', 'sept.', 'oct.', 'nov.', 'dГ©c.'],
		dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
		dayNamesShort: ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'],
		dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['fr-CA']);
});
