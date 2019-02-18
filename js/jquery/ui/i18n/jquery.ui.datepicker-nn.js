/* Norwegian Nynorsk initialisation for the jQuery UI date picker plugin. */
/* Written by BjГёrn Johansen (post@bjornjohansen.no). */
jQuery(function($){
	$.datepicker.regional['nn'] = {
		closeText: 'Lukk',
		prevText: '&#xAB;FГёrre',
		nextText: 'Neste&#xBB;',
		currentText: 'I dag',
		monthNames: ['januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember'],
		monthNamesShort: ['jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','des'],
		dayNamesShort: ['sun','mГҐn','tys','ons','tor','fre','lau'],
		dayNames: ['sundag','mГҐndag','tysdag','onsdag','torsdag','fredag','laurdag'],
		dayNamesMin: ['su','mГҐ','ty','on','to','fr','la'],
		weekHeader: 'Veke',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['nn']);
});
