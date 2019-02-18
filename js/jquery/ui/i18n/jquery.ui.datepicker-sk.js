/* Slovak initialisation for the jQuery UI date picker plugin. */
/* Written by Vojtech Rinik (vojto@hmm.sk). */
jQuery(function($){
	$.datepicker.regional['sk'] = {
		closeText: 'ZavrieЕҐ',
		prevText: '&#x3C;PredchГЎdzajГєci',
		nextText: 'NasledujГєci&#x3E;',
		currentText: 'Dnes',
		monthNames: ['januГЎr','februГЎr','marec','aprГ­l','mГЎj','jГєn',
		'jГєl','august','september','oktГіber','november','december'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','MГЎj','JГєn',
		'JГєl','Aug','Sep','Okt','Nov','Dec'],
		dayNames: ['nedeДѕa','pondelok','utorok','streda','ЕЎtvrtok','piatok','sobota'],
		dayNamesShort: ['Ned','Pon','Uto','Str','Е tv','Pia','Sob'],
		dayNamesMin: ['Ne','Po','Ut','St','Е t','Pia','So'],
		weekHeader: 'Ty',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['sk']);
});
