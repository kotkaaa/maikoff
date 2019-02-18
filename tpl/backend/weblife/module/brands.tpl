<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.BRANDS creat_title=$smarty.const.ADMIN_CREATING_NEW_BRAND edit_title=$smarty.const.ADMIN_EDIT_BRAND}><{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
    <form method="POST" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
        <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
        <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
        <input type="hidden" name="order"   value="<{$item.order}>"   />
        <div class="tabsContainer">
            <ul class="nav">
                <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
                <li><a href="javascript:void(0);" data-target="series">Серии</a></li>
                <li><a href="javascript:void(0);" data-target="seo">SEO</a></li>
                <li><a href="javascript:void(0);" data-target="history">История</a></li>
            </ul>
            <div class="tab_line"></div>
            <ul class="tabs">
                <li class="active" id="tab_main">
                    <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                        <tr>
                            <td id="headb" align="left"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                            <td>
                                <input class="left" name="title" size="70" id="title" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.title}>" /> <input type="button" class="buttons left" value="Изменить SEO путь" onclick="MoveToSeoPath();"/>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                        <tr>
                            <td id="headb" align="left"><{$smarty.const.HEAD_PUBLISH_PAGE}></td>
                            <td align="left">
                                <input type="radio" name="active" value="1" <{if $item.active==1}>checked<{/if}>>
                                <{$smarty.const.OPTION_YES}>
                                <input type="radio" name="active" value="0" <{if $item.active==0}>checked<{/if}>>
                                <{$smarty.const.OPTION_NO}>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <!-- ++++++++++ Start Attach Files ++++++++++++++++++++++++++++++++++++++++++++++ -->
                        <{include file='common/attach_files.tpl' item=$item attachFile=false attachImages=true}>
                        <!-- ++++++++++ End Attach Files ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        <tr>
                            <td colspan="2">
                                <strong><{$smarty.const.HEAD_SHORT_CONTENT}></strong>
                                <a href="javascript:toggleEditor('description');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                                <textarea style="width:840px; height: 100px;" id="description" name="descr" ><{$item.descr}></textarea>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                    </table>
                </li>
                <li id="tab_series">
                    <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                        <tr>
                            <td>
                                <div id="tip"></div>
                                <div class="left">
                                    <input id="newSeries" style="margin-top:5px; height:24px; padding-left:5px;" type="text" value="" placeholder="введите название" class="nosize_field" size="104"/>&nbsp;&nbsp;&nbsp;
                                </div>
                                <div class="left">
                                    <input type="button" class="buttons" value="Добавить" onclick="addNewSeries();"/>
                                </div>
                                <div class="clear"></div><br/>
                                <strong>Название</strong>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                                <strong>SEO путь</strong><br/>
                                <div class="sortable-wrapper" style="width:100%; margin-top: 4px;">
                                    <ul class="sortable" id="defaultVals">
<{foreach name=i from=$item.series key=arKey item=arValue}>
                                        <li class="ui-state-default attrsort">
                                            <input type="hidden" name="series[<{$arKey}>][id]" value="<{$arKey}>"/>
                                            <input class="left field" type="text" name="series[<{$arKey}>][title]" value="<{$arValue.title}>" style="width: 170px;"/>
                                            <input class="left field" type="text" name="series[<{$arKey}>][seo_path]" value="<{$arValue.seo_path}>" style="width: 170px;"/>
                                            <input type="button" value="Генерировать" style="float: left; margin: 3px 5px 0 0; min-width: 100px;" class="buttons" onclick="if(this.form['series[<{$arKey}>][title]'].value.length==0){alert('Вы не ввели значение атрибута!'); this.form['series[<{$arKey}>][title]'].focus(); return false; } else{ generateSeoPath(this.form['series[<{$arKey}>][seo_path]'], this.form['series[<{$arKey}>][title]'].value, this.form.title.value);}">
<{if $arValue.edit}>
                                            <a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);">
                                                <img src="images/admin/error.png">
                                            </a>&nbsp;
<{/if}>
                                            <img class="right" src="images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" style="margin-left: 15px;">
                                            <a class="right" href="/admin.php?module=series&task=editItem&itemID=<{$arKey}>&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'Редактирование серии', objectType:'iframe', preserveContent: false, width:960})">
                                                <img class="right" src="images/admin/settings.png" title="Нажмите и перетащите элемент на новое место в списке">
                                            </a>
                                            <div class="clear"></div>
                                        </li>
<{/foreach}>
                                    </ul>
                                </div>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                    </table>
                </li>
                <li id="tab_seo">
                    <table width="101%" border="0" cellspacing="0" cellpadding="0" class="sheet" >
                        <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                        <{include file='common/meta_seo_data.tpl'}>
                        <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    </table>
                </li>
                <li id="tab_history">
                    <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
                </li>
            </ul>
        </div>
    </form>
    <script type="text/javascript">
        $(function(){
            $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
            $(document).keypress(function(e){
                if (e.which == 13 && $('#newSeries').val().length>0){
                    addNewSeries();
                    return false;
                }
            });
            $('#newSeries').autocomplete({
                source: function(request, response) {
                    var arrValues = {};
                    $.each($('ul.sortable').find('li').find('.field'), function() {
                        if($(this).val().indexOf(request.term)!=-1) {
                            arrValues[$(this).attr('name')] = $(this).val();
                        }
                    });
                    response($.map(arrValues, function(item, i) {
                        return {
                            label: item,
                            value: item,
                            name: i
                        }
                    }));
                },
                select: function(event, ui) {
                    $('ul.sortable').scrollTop($('ul.sortable').find('input[name="'+ui.item.name+'"]').position().top);
                    $('ul.sortable').find('input[name="'+ui.item.name+'"]').focus();
                    $(this).val("");
                    return false;
                },
                minLength: 2
            });
        });
        function formCheck(form) {
            if(form.title.value.length == 0){
               alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>');
               return false;
            } return true;
        }
        function addNewSeries() {
            if( $('#attrType option:selected').val()==2 && !isNumber($('#newSeries').val())){
                $('#tip').text('Введите число или измените тип на "Текстовый"');
                $('#newSeries').addClass('error');
                return false;
            } else {
                if($('#newSeries').val().length>0) {
                    $('#newSeries').removeClass('error');
                    var maxID = <{if isset($item.seriesMaxID)}><{$item.seriesMaxID}> + <{/if}>$('ul.sortable').find('li').length,
                        html = '<li class="ui-state-default attrsort">'+
                               '<input type="hidden" name="series['+maxID+'][id]" value="0"/>'+
                               '<input name="series['+maxID+'][title]" class="left field" type="text" value="'+$('#newSeries').val()+'" style="width:170px;"/>'+
                               '<input name="series['+maxID+'][seo_path]" class="left field" type="text" value="" style="width:170px;"/>'+
                               '<input type="button" value="Генерировать" style="float: left; margin: 3px 5px 0 0; min-width: 100px;" class="buttons" onclick="if(this.form[\'series['+maxID+'][title]\'].value.length==0){alert(\'Вы не ввели значение атрибута!\'); this.form[\'series['+maxID+'][title]\'].focus(); return false; } else{ generateSeoPath(this.form[\'series['+maxID+'][seo_path]\'], this.form[\'series['+maxID+'][title]\'].value, this.form.title.value);}">'+
                               '<a class="right" href="javascript:;" onclick="removeAttrVal(this);">'+
                               '<img src="images/admin/error.png"/></a>'+
                               '<img class="right" title="Нажмите и перетащите элемент на новое место в списке" src="images/sort.png"/>'+
                               '<div class="clear"></div>'+
                               '</li>';
                    $('ul.sortable').append(html);
                    $('#newSeries').val('');
                    $('#tip').text('');
                    $('.sortable').find('input[type="text"]').mousedown(function(e){
                        e.stopPropagation();
                    });
                }
            }
        }
        function removeAttrVal(item, removeAll) {
            if (typeof removeAll == 'undefined') $(item).parent().remove();
            else $('ul.sortable').html('');
        }
    </script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    <{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_BRAND}>
    <div class="clear"></div>
    <form method="POST" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored">
            <tr>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="center" width="30"></td>
                <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
<{section name=i loop=$items}>
            <tr>
               <td align="center">
<{if $items[i].active==1}>
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="<{$smarty.const.HEAD_NO_PUBLISH}>">
                        <img src="<{$arrPageData.system_images}>check.png" alt="<{$smarty.const.HEAD_NO_PUBLISH}>" title="<{$smarty.const.HEAD_NO_PUBLISH}>" />
                    </a>
<{else}>
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="<{$smarty.const.HEAD_PUBLISH}>">
                        <img src="<{$arrPageData.system_images}>un_check.png" alt="<{$smarty.const.HEAD_PUBLISH}>" title="<{$smarty.const.HEAD_PUBLISH}>" />
                    </a>
<{/if}>
                </td>
                <td align="center" valign="center" style="height:30px; width: 30px;">
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>">
                        <img style="max-width:30px; max-height:30px;" src="<{if $items[i].image}><{$arrPageData.files_url|cat:$items[i].image}><{else}><{$arrPageData.files_url|cat:'noimage.jpg'}><{/if}>"/>
                    </a>
                </td>
                <td>
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a>
                </td>
                <td align="center">
                    <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
                </td>
                <td align="center" >
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                        <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                    </a>
                </td>
                <td align="center">
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
                       <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
                    </a>
                </td>
            </tr>
<{/section}>
        </table>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr>
                <td align="center" width="350"></td>
                <td align="center" width="350">
<{if $arrPageData.total_pages>1}>
                    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
                </td>
                <td align="right">
                    <input name="submit_order" class="buttons" type="submit" value="<{$smarty.const.BUTTON_APPLY}>" />
                </td>
            </tr>
        </table>
    </form>
<{/if}>
</div>