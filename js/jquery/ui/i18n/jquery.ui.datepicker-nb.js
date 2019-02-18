/* Norwegian BokmГҐl initialisation for the jQuery UI date picker plugin. */
/* Written by BjГёrn Johansen (post@bjornjohansen.no). */
jQuery(function($){
	$.datepicker.regional['nb'] = {
		closeText: 'Lukk',
		prevText: '&#xAB;Forrige',
		nextText: 'Neste&#xBB;',
		currentText: 'I dag',
		monthNames: ['januar','februar','mars','april','mai','juni','juli','august','september','oktober','november','desember'],
		monthNamesShort: ['jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','des'],
		dayNamesShort: ['sГёn','man','tir','ons','tor','fre','lГёr'],
		dayNames: ['sГёndag','mandag','tirsdag','onsdag','torsdag','fredag','lГёrdag'],
		dayNamesMin: ['sГё','ma','ti','on','to','fr','lГё'],
		weekHeader: 'Uke',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['nb']);
});
