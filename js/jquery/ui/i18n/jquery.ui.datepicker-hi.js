/* Hindi initialisation for the jQuery UI date picker plugin. */
/* Written by Michael Dawart. */
jQuery(function($){
	$.datepicker.regional['hi'] = {
		closeText: 'а¤¬а¤‚а¤¦',
		prevText: 'а¤Єа¤їа¤›а¤Іа¤ѕ',
		nextText: 'а¤…а¤—а¤Іа¤ѕ',
		currentText: 'а¤†а¤њ',
		monthNames: ['а¤ња¤Ёа¤µа¤°аҐЂ ','а¤«а¤°а¤µа¤°аҐЂ','а¤®а¤ѕа¤°аҐЌа¤љ','а¤…а¤ЄаҐЌа¤°аҐ‡а¤І','а¤®а¤€','а¤њаҐ‚а¤Ё',
		'а¤њаҐ‚а¤Іа¤ѕа¤€','а¤…а¤—а¤ёаҐЌа¤¤ ','а¤ёа¤їа¤¤а¤®аҐЌа¤¬а¤°','а¤…а¤•аҐЌа¤џаҐ‚а¤¬а¤°','а¤Ёа¤µа¤®аҐЌа¤¬а¤°','а¤¦а¤їа¤ёа¤®аҐЌа¤¬а¤°'],
		monthNamesShort: ['а¤ња¤Ё', 'а¤«а¤°', 'а¤®а¤ѕа¤°аҐЌа¤љ', 'а¤…а¤ЄаҐЌа¤°аҐ‡а¤І', 'а¤®а¤€', 'а¤њаҐ‚а¤Ё',
		'а¤њаҐ‚а¤Іа¤ѕа¤€', 'а¤…а¤—', 'а¤ёа¤їа¤¤', 'а¤…а¤•аҐЌа¤џ', 'а¤Ёа¤µ', 'а¤¦а¤ї'],
		dayNames: ['а¤°а¤µа¤їа¤µа¤ѕа¤°', 'а¤ёаҐ‹а¤®а¤µа¤ѕа¤°', 'а¤®а¤‚а¤—а¤Іа¤µа¤ѕа¤°', 'а¤¬аҐЃа¤§а¤µа¤ѕа¤°', 'а¤—аҐЃа¤°аҐЃа¤µа¤ѕа¤°', 'а¤¶аҐЃа¤•аҐЌа¤°а¤µа¤ѕа¤°', 'а¤¶а¤Ёа¤їа¤µа¤ѕа¤°'],
		dayNamesShort: ['а¤°а¤µа¤ї', 'а¤ёаҐ‹а¤®', 'а¤®а¤‚а¤—а¤І', 'а¤¬аҐЃа¤§', 'а¤—аҐЃа¤°аҐЃ', 'а¤¶аҐЃа¤•аҐЌа¤°', 'а¤¶а¤Ёа¤ї'],
		dayNamesMin: ['а¤°а¤µа¤ї', 'а¤ёаҐ‹а¤®', 'а¤®а¤‚а¤—а¤І', 'а¤¬аҐЃа¤§', 'а¤—аҐЃа¤°аҐЃ', 'а¤¶аҐЃа¤•аҐЌа¤°', 'а¤¶а¤Ёа¤ї'],
		weekHeader: 'а¤№а¤«аҐЌа¤¤а¤ѕ',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['hi']);
});
