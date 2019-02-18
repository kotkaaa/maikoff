/* Icelandic initialisation for the jQuery UI date picker plugin. */
/* Written by Haukur H. Thorsson (haukur@eskill.is). */
jQuery(function($){
	$.datepicker.regional['is'] = {
		closeText: 'Loka',
		prevText: '&#x3C; Fyrri',
		nextText: 'NГ¦sti &#x3E;',
		currentText: 'ГЌ dag',
		monthNames: ['JanГєar','FebrГєar','Mars','AprГ­l','MaГ­','JГєnГ­',
		'JГєlГ­','ГЃgГєst','September','OktГіber','NГіvember','Desember'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','MaГ­','JГєn',
		'JГєl','ГЃgГє','Sep','Okt','NГіv','Des'],
		dayNames: ['Sunnudagur','MГЎnudagur','ГћriГ°judagur','MiГ°vikudagur','Fimmtudagur','FГ¶studagur','Laugardagur'],
		dayNamesShort: ['Sun','MГЎn','Гћri','MiГ°','Fim','FГ¶s','Lau'],
		dayNamesMin: ['Su','MГЎ','Гћr','Mi','Fi','FГ¶','La'],
		weekHeader: 'Vika',
		dateFormat: 'dd/mm/yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['is']);
});
