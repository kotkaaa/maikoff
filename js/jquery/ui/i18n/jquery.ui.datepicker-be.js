/* Belarusian initialisation for the jQuery UI date picker plugin. */
/* Written by Pavel Selitskas <p.selitskas@gmail.com> */
jQuery(function($){
	$.datepicker.regional['be'] = {
		closeText: 'Р—Р°С‡С‹РЅС–С†СЊ',
		prevText: '&larr;РџР°РїСЏСЂ.',
		nextText: 'РќР°СЃС‚.&rarr;',
		currentText: 'РЎС‘РЅСЊРЅСЏ',
		monthNames: ['РЎС‚СѓРґР·РµРЅСЊ','Р›СЋС‚С‹','РЎР°РєР°РІС–Рє','РљСЂР°СЃР°РІС–Рє','РўСЂР°РІРµРЅСЊ','Р§СЌСЂРІРµРЅСЊ',
		'Р›С–РїРµРЅСЊ','Р–РЅС–РІРµРЅСЊ','Р’РµСЂР°СЃРµРЅСЊ','РљР°СЃС‚СЂС‹С‡РЅС–Рє','Р›С–СЃС‚Р°РїР°Рґ','РЎСЊРЅРµР¶Р°РЅСЊ'],
		monthNamesShort: ['РЎС‚Сѓ','Р›СЋС‚','РЎР°Рє','РљСЂР°','РўСЂР°','Р§СЌСЂ',
		'Р›С–Рї','Р–РЅС–','Р’РµСЂ','РљР°СЃ','Р›С–СЃ','РЎСЊРЅ'],
		dayNames: ['РЅСЏРґР·РµР»СЏ','РїР°РЅСЏРґР·РµР»Р°Рє','Р°СћС‚РѕСЂР°Рє','СЃРµСЂР°РґР°','С‡Р°С†СЊРІРµСЂ','РїСЏС‚РЅС–С†Р°','СЃСѓР±РѕС‚Р°'],
		dayNamesShort: ['РЅРґР·','РїРЅРґ','Р°СћС‚','СЃСЂРґ','С‡С†РІ','РїС‚РЅ','СЃР±С‚'],
		dayNamesMin: ['РќРґ','РџРЅ','РђСћ','РЎСЂ','Р§С†','РџС‚','РЎР±'],
		weekHeader: 'РўРґ',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['be']);
});
