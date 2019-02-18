/* Kyrgyz (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Sergey Kartashov (ebishkek@yandex.ru). */
jQuery(function($){
	$.datepicker.regional['ky'] = {
		closeText: 'Р–Р°Р±СѓСѓ',
		prevText: '&#x3c;РњСѓСЂ',
		nextText: 'РљРёР№&#x3e;',
		currentText: 'Р‘ТЇРіТЇРЅ',
		monthNames: ['РЇРЅРІР°СЂСЊ','Р¤РµРІСЂР°Р»СЊ','РњР°СЂС‚','РђРїСЂРµР»СЊ','РњР°Р№','РСЋРЅСЊ',
		'РСЋР»СЊ','РђРІРіСѓСЃС‚','РЎРµРЅС‚СЏР±СЂСЊ','РћРєС‚СЏР±СЂСЊ','РќРѕСЏР±СЂСЊ','Р”РµРєР°Р±СЂСЊ'],
		monthNamesShort: ['РЇРЅРІ','Р¤РµРІ','РњР°СЂ','РђРїСЂ','РњР°Р№','РСЋРЅ',
		'РСЋР»','РђРІРі','РЎРµРЅ','РћРєС‚','РќРѕСЏ','Р”РµРє'],
		dayNames: ['Р¶РµРєС€РµРјР±Рё', 'РґТЇР№С€У©РјР±ТЇ', 'С€РµР№С€РµРјР±Рё', 'С€Р°СЂС€РµРјР±Рё', 'Р±РµР№С€РµРјР±Рё', 'Р¶СѓРјР°', 'РёС€РµРјР±Рё'],
		dayNamesShort: ['Р¶РµРє', 'РґТЇР№', 'С€РµР№', 'С€Р°СЂ', 'Р±РµР№', 'Р¶СѓРј', 'РёС€Рµ'],
		dayNamesMin: ['Р–Рє','Р”С€','РЁС€','РЁСЂ','Р‘С€','Р–Рј','РС€'],
		weekHeader: 'Р–СѓРј',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['ky']);
});
