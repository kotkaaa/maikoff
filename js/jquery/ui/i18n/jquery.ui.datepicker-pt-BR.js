/* Brazilian initialisation for the jQuery UI date picker plugin. */
/* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
jQuery(function($){
	$.datepicker.regional['pt-BR'] = {
		closeText: 'Fechar',
		prevText: '&#x3C;Anterior',
		nextText: 'PrГіximo&#x3E;',
		currentText: 'Hoje',
		monthNames: ['Janeiro','Fevereiro','MarГ§o','Abril','Maio','Junho',
		'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
		'Jul','Ago','Set','Out','Nov','Dez'],
		dayNames: ['Domingo','Segunda-feira','TerГ§a-feira','Quarta-feira','Quinta-feira','Sexta-feira','SГЎbado'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','SГЎb'],
		dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','SГЎb'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
});
