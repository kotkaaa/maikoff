function openTab (url) {
    window.open(url);
}

function popUp(url) {
    var arWindowInfo = new Array();
    arWindowInfo['w'] = 640;
    arWindowInfo['h'] = 480;
    arWindowInfo['l'] = 300;
    arWindowInfo['t'] = 200;
    popUpExact(url, arWindowInfo);
}

function popUpExact(url, arWindowInfo) {
    day = new Date();
    id = day.getTime();
    eval("page" + id + " = window.open(url, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width="+arWindowInfo['w']+",height="+arWindowInfo['h']+",left="+arWindowInfo['l']+",top="+arWindowInfo['t']+"');");
}

function manageSelections(bt, sel1, sel2) {
    if(bt.checked){
        sel1.disabled = true;
        sel2.disabled = false;
    } else {
        sel1.disabled = false;
        sel2.disabled = true;
    }
}

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function hideApplyBut(sel, but, parentID) {
    if(parentID != sel.value){
        $(but).css('display', 'none');
    } else if(parentID == sel.value){
        $(but).css('display', '');
    }
}

function toggleBox(a, elementId) {
    if($('#'+elementId).css('display')=='none'){
        $('#'+elementId).css('display', '');
        $(a).removeClass( 'down' );
        $(a).addClass( 'up' );
    } else {
        $('#'+elementId).css('display', 'none');
        $(a).removeClass( 'up' );
        $(a).addClass( 'down' );
    }
}

function toggleByClass(a, elementId, classname) {
    if($('#'+elementId+' .'+classname).hasClass('hidden_block')){
        $('#'+elementId+' .'+classname).removeClass('hidden_block');
        $(a).removeClass( 'down' );
        $(a).addClass( 'up' );
    } else {
        $('#'+elementId+' .'+classname).addClass('hidden_block');
        $(a).removeClass( 'up' );
        $(a).addClass( 'down' );
    }
}

function clearInput(elementId) {
    document.getElementById(elementId).value = '';
}

function generateSeoPath(obj, str, pref){
    $.getJSON(
        "/interactive/ajax.php",
        {zone: "admin", action: "generateSeoPath", path: encodeURIComponent(str), prefix: encodeURIComponent(pref)},
        function(data, textStatus){
            if(textStatus=='success'){
                if(data.seo_path!='') obj.value = data.seo_path;
            }
        }
    );
}

function showImg(el, url){
    $('#image').attr('src', url);
    $('#image').removeClass('hidden_block');
    $(el).mousemove(function(e) {
        $('#image').offset({
          top: e.pageY - $('#image').outerHeight(),
          left: e.pageX - $('#image').outerWidth()
        });
    }).mouseleave(function() {
        $('#image').addClass('class', 'hidden_block');
        $('#image').attr('src', '');
    });
} 

function empty(mixed_var) {
    return (mixed_var==="" || mixed_var===0 || mixed_var==="0" || mixed_var===null || mixed_var===false || mixed_var==='undefined' || typeof(mixed_var)==='undefined' || (is_array(mixed_var) && mixed_var.length===0));
}

function is_array( mixed_var ) {
    return ( mixed_var instanceof Array );
    }

function is_object( mixed_var ){
    if(mixed_var instanceof Array) {
        return false;
    } else {
        return (mixed_var !== null) && (typeof( mixed_var ) == 'object');
    }
}

function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key]===needle) || (!strict && haystack[key]==needle)) {
            found = true;
            break;
        }
    }
    return found;
}

function array_key_exists (key, search) {
    if(!search || (search.constructor !== Array && search.constructor !== Object)){
        return false;
    }
    return search[key] !== undefined;
}

function count( mixed_var, mode ) {
	var key, cnt = 0;
	if( mode == 'COUNT_RECURSIVE' ) mode = 1;
	if( mode != 1 ) mode = 0;
	for (key in mixed_var){
            cnt++;
            if( mode==1 && mixed_var[key] && (mixed_var[key].constructor === Array || mixed_var[key].constructor === Object) ){
                cnt += count(mixed_var[key], 1);
            }
	}
	return cnt;
}

function strpos (haystack, needle, offset) {
	var i = haystack.indexOf( needle, offset ); // returns -1
	return i >= 0 ? i : false;
}


function SelectCheckBox(cb, container){  
    if(typeof container == 'undefind')
        container = 'document';
    if($(cb).hasClass('check_all')) {
        $(".checkboxes", container).prop('checked', $(cb).prop('checked'));
    } else {
        var inputs_count = $(".checkboxes:not(.check_all)", container).length;
        var checked_inputs_count = $(".checkboxes:checked:not(.check_all)", container).length;
        if(inputs_count>0){
           $(".checkboxes.check_all", container).prop('checked', (inputs_count == checked_inputs_count) ? true : false);
        }
    }
}

function toggleHiddenFields() {
    var arItems = $('table.list').find('td.hidden');
    $(arItems).each(function(i, item){
        if($(item).is(':hidden') == false) {
            $(item).hide();
        } else {
            $(item).show();
        }
    });
}

Tabs = {
    container: '.tabsContainer',
    setUp: function() {
        var _self = this;
        
        $(document).find(_self.container).each(function(c, container){
            var controls = $(container).children('.nav').children('li').children('a');
            var tabs = $(container).children('.tabs').children('li');

            $(controls).bind('click', function(){
                $(controls).each(function(i, el){  
                    $(el).removeClass('active');
                });
                $(tabs).each(function(i, el){  
                    $(el).removeClass('active');
                });
                var target = $(container).children('.tabs').children('li#tab_'+$(this).data('target'));
                $(this).addClass('active');
                $(target).addClass('active');
                location.hash = $(this).data('target');
                if($('#currentTab').length) $('#currentTab').val($(this).data('target'));
            });
        });

        if(location.hash) {
            $('a[data-target="'+location.hash.replace('#', '')+'"]').click();
        }
    }
}

function initDatePickers(selector, dateFormat, timePicker) {    
    var timePicker = timePicker||false;
    var selector = selector||$('.datepicker');
    $.each($(selector), function() {
        var presetDate = $(this).hasClass('preset');
        // enhanced range init  http://www.daterangepicker.com/
        if($(this).hasClass('datetimerange') && (typeof $(this).data('name') != "undefined" && $('#'+$(this).data('name')+'_from').length > 0 && $('#'+$(this).data('name')+'_to').length > 0)) {
            var dateFormat = "DD.MM.YYYY"+(timePicker ? " HH:mm:00" : "");
            var from = $('#'+$(this).data('name')+'_from');
            var to = $('#'+$(this).data('name')+'_to');   
            var options = {
                timePicker: timePicker,
                timePicker24Hour: true,
                autoUpdateInput: false,
                ranges: {
                    'Сегодня': [moment(), moment()],
                    'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                    'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    format: dateFormat,
                    applyLabel: "Применить",
                    cancelLabel: "Отмена",
                    fromLabel: "С",
                    toLabel: "По",
                    customRangeLabel: "Свой",
                    weekLabel: "Н",
                    daysOfWeek: ["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],
                    monthNames: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
                    firstDay: 1
                }
            };
            if(presetDate || $(this).data('from').length > 0) {
                options['startDate'] = $(this).data('from').length > 0 ? $(this).data('from') : moment().startOf('month');
                options['endDate'] = $(this).data('to').length > 0 ? $(this).data('to') : moment().endOf('month');
                options['autoUpdateInput'] = true;
            }            
            $(this).daterangepicker(options, function(start, end, label) {
                //console.log("A new date selection was made: " + start.format('YYYY-MM-DD HH:mm') + ' to ' + end.format('YYYY-MM-DD HH:mm'));
                $(from).val(start.format('DD-MM-YYYY'+(timePicker ? ' HH:mm:00' : '')));
                $(to).val(end.format('DD-MM-YYYY'+(timePicker ? ' HH:mm:00' : '')));
            });
            if(presetDate == false) {
                $(this).on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(dateFormat) + ' - ' + picker.endDate.format(dateFormat));
                });
                $(this).on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
            }
        }
//        // simple range init
//        else if(typeof $(this).data('range') != "undefined" && $('#'+$(this).data('range')+'_from').length > 0 && $('#'+$(this).data('range')+'_to').length > 0) {
//            var from = $('#'+$(this).data('range')+'_from');
//            var to = $('#'+$(this).data('range')+'_to');            
//            $(from).datepicker({
//                dateFormat: dateFormat,            
//            });
//            $(to).datepicker({
//                dateFormat: dateFormat,            
//            });
//            $(from).on('change', function(){ 
//                $(to).datepicker('option', 'minDate', $(from).val()); 
//            });             
//            $(to).on('change', function(){ 
//                $(from).datepicker('option', 'maxDate', $(to).val()); 
//            });        
//        } 
        // default datepicker
        else {
            var dateFormat = dateFormat||"dd.mm.yy";
            $(this).datepicker({
                dateFormat: dateFormat,            
            });
            if(presetDate) {
                $(this).datepicker("setDate", new Date());
            }
        }
    });        
}

function MoveToSeoPath() {
    $('.tabsContainer').find('a.active').removeClass('active');
    $('.tabsContainer').find('li.active').removeClass('active');  
    $('.tabsContainer').find('#tab_seo').addClass('active');
    $('.tabsContainer').find('a[data-target="seo"]').addClass('active');
    $('#seo_path').focus();
}
