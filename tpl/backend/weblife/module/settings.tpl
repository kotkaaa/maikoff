<{include file='common/module_head.tpl' 
          title=$smarty.const.TITLE_SETTINGS 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_PRODUCT 
          edit_title=$smarty.const.ADMIN_EDIT_PRODUCT 
}>

<div id="right_block">
<form method="post" action="<{$arrPageData.current_url}>" name="settingsForm">
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" <{if empty($arrPageData.activeTab)}>class="active"<{/if}>>Основные</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
            <li>
                <a href="javascript:void(0);" data-target="actionsLog"
                   <{if !empty($arrPageData.activeTab) && $arrPageData.activeTab=='actionsLog'}>class="active"<{/if}>>
                    Глобальная История
                </a>
            </li>       
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li <{if empty($arrPageData.activeTab)}>class="active"<{/if}> id="tab_main">
                <table border="0" cellspacing="0" cellpadding="1" class="sheet">  
                    <tr>
                        <td colspan="2" align="left">
                            <div class="inline"><{$smarty.const.SETTINGS_PHONE}> <font color="red">*</font>:</div>
                            <input name="arValidate[sitePhone]" type="hidden" value="<{$smarty.const.SETTINGS_PHONE}>"/>
                            <input name="arSettings[sitePhone]" type="text" size="117" value="<{if isset($item.sitePhone)}><{$item.sitePhone}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline"><{$smarty.const.SETTINGS_PHONE}> (Adwords):</div>
                            <input name="arSettings[adwordsPhone]" type="text" size="117" value="<{if isset($item.adwordsPhone)}><{$item.adwordsPhone}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline">Название сайта (для емейлов) <font color="red">*</font>:</div>
                            <input name="arSettings[websiteName]" type="text" size="117" value="<{if isset($item.websiteName)}><{$item.websiteName}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline">E-mail для уведомлений админу<font color="red">*</font>:</div>
                            <input name="arValidate[ownerEmail]" type="hidden" value="<{$smarty.const.SETTINGS_SITE_EMAIL}>"/>
                            <input name="arSettings[ownerEmail]" type="text" size="117" value="<{if isset($item.ownerEmail)}><{$item.ownerEmail}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline">Контактный e-mail <font color="red">*</font>:</div>
                            <input name="arValidate[siteEmail]" type="hidden" value="<{$smarty.const.SETTINGS_SITE_EMAIL}>"/>
                            <input name="arSettings[siteEmail]" type="text" size="117" value="<{if isset($item.siteEmail)}><{$item.siteEmail}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline">Контактный e-mail (Adwords):</div>
                            <input name="arSettings[adwordsEmail]" type="text" size="117" value="<{if isset($item.adwordsEmail)}><{$item.adwordsEmail}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline">E-mail для уведомлений <font color="red">*</font>:</div>
                            <input name="arValidate[notifyEmail]" type="hidden" value="<{$smarty.const.SETTINGS_NOTIFY_EMAIL}>"/>
                            <input name="arSettings[notifyEmail]" type="text" size="117" value="<{if isset($item.notifyEmail)}><{$item.notifyEmail}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline"><{$smarty.const.SETTINGS_COPYRIGHT}>:</div>
                            <input name="arSettings[copyright]" size="117" type="text" value="<{if isset($item.copyright)}><{$item.copyright}><{/if}>" class="field"/>
                            <br/><br/>
                            <div class="inline"><{$smarty.const.SETTINGS_ADDRESS}>:</div><br/>
                            <textarea style="width:840px; height: 100px;" name='arSettings[ownerAddress]' cols='105' rows='5' class="field"><{if isset($item.ownerAddress)}><{$item.ownerAddress}><{/if}></textarea>
                            <br/><br/>
                            <div class="inline">График работы:</div><br/>
                            <textarea style="width:840px; height: 100px;" name='arSettings[schedule]' cols='105' rows='5' class="field"><{if isset($item.schedule)}><{$item.schedule}><{/if}></textarea>
                            <br/><br/>
                            <div class="inline" title="Дробные значения разделяются точкой, например, 33.4">Курс евро <font color="red">*</font> :</div>
                            <input name="arValidate[eurRate]" type="hidden" value="Введите курс евро|number"/>
                            <input name="arSettings[eurRate]" type="text" size="5" value="<{if isset($item.eurRate)}><{$item.eurRate}><{/if}>" class="field"/> грн <i>(дробные значения разделяются точкой, например, 33.4)</i>
                            <br/><br/>
                            <div class="inline" title="Количество десятичных знаков, до которых округлять цену в каталоге одежды" style="width:300px">При конвертации цену на сайте округлять <font color="red">*</font>:</div>
                            <select name="arSettings[pricePrecision]">
                                <{foreach from=$arrPageData.arPrecisions key=value item=precision}>
                                    <option value="<{$value}>"<{if isset($item.pricePrecision) && $item.pricePrecision == $value}> selected<{/if}>><{$precision}></option>
                                <{/foreach}>
                            </select>
                            <br/><br/>
                        </td>
                        <td class="buttons_row" valign="top" width="144">
                            <div class="buttons_list">
                                <input name='submit' class='buttons' type='submit' value='<{$smarty.const.BUTTON_SAVE}>'/>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
             <li id="tab_actionsLog" <{if !empty($arrPageData.activeTab) && $arrPageData.activeTab=='actionsLog'}>class="active"<{/if}>>
                <div class="load"></div>
                <table border="0" cellspacing="0" cellpadding="1" class="sheet">  
                    <tr>
                        <td valign="top" width="175">
                            <div id="filters" class="filters">
                                <{include file="ajax/actions_log_filters.tpl" arFilters=$arActionsLog.arFilters selectedFilters=$arActionsLog.selectedFilters}>
                            </div>
                        </td>
                        <td valign="top">
                            <div id="history">
                                <{include file="ajax/actions_log.tpl" arHistoryData=$arActionsLog.arHistory}>
                            </div>
                        </td>
                    </tr>
                </table>
             </li>
         </ul>
     </div>
</form>
</div>
                            
<script type=text/javascript>
    function clearDate() {
        $('#date_to').val('');
        $('#date_from').val('');
        updateHistory();
    }
    
    function ConvertDateText(dateText, date_sep){
        if(dateText.length > 0){
            if(date_sep===undefined) date_sep = '-';
            var arr = [];

            var arDateTime = dateText.split(' ');
           // if(arDateTime.length==2) {
                if(arDateTime[0].indexOf('.')!==-1){
                    arr = arDateTime[0].split('.');
                    if(date_sep==='-') arr.reverse();
                } else if(arDateTime[0].indexOf('-')!==-1){
                    arr = arDateTime[0].split('-');
                    if(date_sep==='.') arr.reverse();
                } else if(arDateTime[0].indexOf('/')!==-1){
                    arr = arDateTime[0].split('/');
                    if((date_sep==='.' && arDateTime[0].match(/^\d{4}\/\d{2}\/\d{2}$/) !== null) || (date_sep==='-' && arDateTime[0].match(/^\d{2}\/\d{2}\/\d{4}$/) !== null))
                        arr.reverse();
                }
                
             /*   if(arDateTime[1].indexOf(':')!==-1){
                    var arTime = arDateTime[1].split(':');
                    for(var i=0; i<arTime.length; i++)
                        if(arTime[i] == '00') arr.push('0');
                        else arr.push(arTime[i]);
                }*/
            //}
        }
        return arr;
    }

    function ConvertToJSDate(dateText){
        var date = '';
        if(dateText.length > 0){
           var d = ConvertDateText(dateText);
           date = +new Date(d[0], d[1]-1, d[2])/1000;
        }
        return date;
    }
        
    function toogleDateTime(cb) {
         var datetime = $('#datetime');
         if(cb.checked && $(cb).hasClass('show')) {
             $(datetime).removeClass('hidden_block');
         } else {
             $(datetime).addClass('hidden_block');
         }
         updateHistory(cb);
    };

    function updateHistory(item, refresh, page) { 
        $('.load').addClass('active');
        $('.load').css('width', $('.load').next('table').width());
        $('.load').css('height', $('.load').next('table').height());
        var arData = {};
        arData['filters[tab]'] = 'actionsLog';
        if(typeof item != 'undefined' && item!=false) arData['key'] = $(item).attr('data-type');
        if(typeof page != 'undefined') arData['filters[page]'] = page;
        if(typeof refresh == 'undefined' || refresh==false){
            $.each($('#filters').find('input, select'), function(i, input){
                if(input.checked || input.selectedIndex || 
                   (input.type =='text' && $('.datetime.show').prop('checked'))){
                    var iName = $(input).attr('name');
                    var iVal;
                    if(input.type=='text') { 
                        if($(input).val().length>0) iVal = ConvertToJSDate($(input).val());
                    }  else    iVal = $(input).val();
                    arData[iName] = iVal;
                }
            });
        } else {
            arData['filters[time]'] = '1';
        }
        
        $.ajax({
            url: '/interactive/ajax.php?zone=admin&action=filterActionsLog',
            type: 'GET',
            dataType: 'json',
            data: arData,
            success: function(json) {
                if(json) {
                    $('#history').html(json.history);
                    $('#filters').html(json.filters);
                }
                if(History.enabled) {
                    History.pushState(null, document.title, '/admin.php?module=<{$arrPageData.module}>'+json.url);
                }
            }
        });
        setTimeout(function () {$('.load').removeClass('active')}, 200);
    };   
</script>                       
