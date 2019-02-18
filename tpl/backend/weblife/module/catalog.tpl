<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title='Список товаров модели '|cat:$arrPageData.arModel.title creat_title='Создание товара модели '|cat:$arrPageData.arModel.title edit_title='Редактирование товара модели '|cat:$arrPageData.arModel.title}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<a href="/admin.php?module=models&task=editItem&itemID=<{$arrPageData.arModel.id}>" class="inline-block" style="margin-right:5px;margin-top:10px;float:right;z-index:10;position:relative;">&#8592; Назад к редактированию модели "<{$arrPageData.arModel.title}>"</a>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
    <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
    <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
    <input type="hidden" name="order"   value="<{$item.order}>"   />
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                    <tr>
                        <td id="headb" align="left" width="120"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                        <td>
                            <input class="left" name="title" size="77" id="title" type="text" value="<{$item.title}>" /> 
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0 disableCopy=1}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr>
                        <td id="headb" align="left" width="120"><{$smarty.const.HEAD_SEO_PATH}></td>
                        <td>
                            <input type="text" size="77" name="seo_path" id="seo_path" value="<{$item.seo_path}>"/>
                            <input type="button" value="<{$smarty.const.HEAD_GENERATE}>" class="buttons inline-block" onclick="if(this.form.title.value.length==0){ alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); this.form.title.focus(); return false; } else{ generateSeoPath(this.form.seo_path, this.form.title.value, '<{$arrPageData.module}>'); }" />
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">Модель</td>
                        <td align="left">
                            <{$arrPageData.arModel.title}><{if $arrPageData.arModel.pcode}> [<{$arrPageData.arModel.pcode}>]<{/if}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">Артикул <font style="color:red">*</font> </td>
                        <td align="left">
                            <input  name="pcode" id="pcode" data-model_pcode="<{$arrPageData.arModel.pcode}>" size="25" type="text" value="<{$item.pcode}>" readonly/> генерируется автоматически как код модели + код цвета
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PRODUCT_PRICE}> <font style="color:red">*</font> </td>
                        <td align="left">
                            <input name="price" id="price" size="15" type="text" value="<{$item.price}>" onchange="$('#price_uah').val(this.value*<{$objSettingsInfo->eurRate}>)"/>  €  —  
                            <input disabled id="price_uah" size="15" type="text" value="<{if $item.price}><{$item.price*$objSettingsInfo->eurRate}><{/if}>" /> грн
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">
                            Цвета <font style="color:red">*</font> 
                        </td>
                        <td align="left">
<{section name=i loop=$arrPageData.arColors}>
<{if $arrPageData.arColors[i].id == $item.color_id}> 
<{assign var=checked value=1}>
<{else}>
<{assign var=checked value=0}>
<{/if}>
                            <div class="inline-block colors-row<{if $arrPageData.arColors[i].disabled}> disabled<{/if}>" style="vertical-align:top">
                                <label <{if $checked}>style="font-weight:bold"<{/if}>>
                                    <div class="color" style="background:#<{$arrPageData.arColors[i].hex}>;"></div>
                                    <div><input type="radio"<{if $arrPageData.arColors[i].disabled}> disabled<{/if}> name="color_id" 
                                                data-pcode="<{$arrPageData.arColors[i].color_code}>" value="<{$arrPageData.arColors[i].id}>"<{if $checked}> checked<{/if}> 
                                                onchange="$(this).closest('td').find('label').css('font-weight', '');if(this.checked) {$(this).closest('label').css('font-weight', 'bold'); $('#pcode').val($('#pcode').data('model_pcode')+($(this).data('pcode') ? '<{$smarty.const.CATALOG_PRODUCT_PCODE_SEPARATOR}>'+$(this).data('pcode') : ''))}"/> 
                                        <{$arrPageData.arColors[i].title}><{if $arrPageData.arColors[i].color_code}><br/><span class="color_code"><{$arrPageData.arColors[i].color_code}></span><{/if}>
                                    </div>
                                </label>
                            </div>
<{/section}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{if !empty($arrPageData.arSizes)}>
                    <tr>
                        <td id="headb" align="left">
                            Размеры
                        </td>
                        <td align="left">
<{section name=i loop=$arrPageData.arSizes}>
<{if in_array($arrPageData.arSizes[i].title, $item.arSizes)}> 
<{assign var=checked value=1}>
<{else}>
<{assign var=checked value=0}>
<{/if}>
                            <label style="padding-right:10px;font-size:15px;<{if $checked}>font-weight:bold<{/if}>">
                                <input type="checkbox" name="arSizes[]" value="<{$arrPageData.arSizes[i].title}>"<{if $checked}> checked<{/if}> onchange="$(this).closest('label').css('font-weight',(this.checked ? 'bold' : ''));"/> <{$arrPageData.arSizes[i].title}>
                            </label>
<{/section}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{/if}>
                    <!-- ++++++++++ Start Attach Files ++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/attach_files.tpl' item=$item attachFile=false attachImages=true}>
                    <!-- ++++++++++ End Attach Files ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <tr>
                        <td id="headb" align="left">
                            Виды печати 
                        </td>
                        <td align="left">
<{section name=i loop=$arrPageData.arPrintTypes}>
                            <label>
                                <input type="checkbox" name="arPrintTypes[]" value="<{$arrPageData.arPrintTypes[i].id}>" <{if in_array($arrPageData.arPrintTypes[i].id, $item.print_types)}>checked<{/if}>/> <{$arrPageData.arPrintTypes[i].title}>
                            </label>
<{/section}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
            </li>

            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>

<script type="text/javascript">
    function formCheck(form) {
        if (form.title.value.length == 0) {
            alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>');
            return false;
        }
        if (form.pcode.value.length == 0) {
            alert('Введите код товара!!!');
            return false;
        }
        if (form.price.value.length == 0) {
            alert('Введите цену товара!!!');
            return false;
        }
        if (form.color_id.value.length == 0 || form.color_id.value == '') {
            alert('Выберите цвет товара!!!');
            return false;
        }
        return true;
    }
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}> 
<{include file='common/new_page_btn.tpl' title='товар'}>
<a href="/admin.php?module=models" class="inline-block" style="margin-top:5px;">&#8592; Назад к списку моделей</a>
<div class="search_block" style="margin-top:-20px;">
    <form method="GET" id="searchForm" action="">
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        <input type="hidden" name="modelID" value="<{$arrPageData.modelID}>" />
        <a href="<{$arrPageData.admin_url}>" class="buttons right" style="margin-top:0;margin-left:3px;height:24px;line-height:24px;color:#fff">Сбросить</a>
        <button type="submit" class="buttons right" style="margin-top:0; margin-left:15px;"><{$smarty.const.SITE_FOUND}></button>
        <input size="48" type="text" class="right" placeholder="поиск по артикулу или названию товара" id="categorySearch" name="filters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" />
    </form>
</div>
<div class="clear"></div>

<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored" id="operationTbl">
        <thead>
            <tr>
                <{if $arrPageData.total_items>1}>
                <td id="headb" align="center" width="12"></td>
                <{/if}>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="center" width="48">арт.</td>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                <td id="headb" align="center" width="62">сорт</td>
                <td id="headb" align="center" width="62"><{$smarty.const.HEAD_PRICE}> &euro;</td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
        </thead>
        <tbody class='sortable-table'>
        <{section name=i loop=$items}>
            <tr class="sortable-tr">
                <{if $arrPageData.total_items>1}>
                <td align="center">
                    <input type="checkbox" class="checkboxes" name="arCheckedItems[<{$items[i].id}>]" onchange="SelectCheckBox(this);" value="1"/>
                </td>
                <{/if}>
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
                <td><{$items[i].pcode}></td>
                <td align="center"><{if $items[i].default_image}><img src="<{$items[i].default_image}>" height="30"/><{/if}></td>
                <td align="left"><{$items[i].title}></td>
                <td align="center">                   
                    <img class="sort-link" src="/images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" style='cursor:move'/>
                </td>
                <td align="center">
                    <input type="text" name="arItems[<{$items[i].id}>]" id="arPrices_<{$items[i].id}>" class="price" value="<{$items[i].price}>" size="5"/>
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
        </tbody>
    </table>

    <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
            <{if $arrPageData.total_items>1}>
                <td width="107" align="left" style="padding:6px">
                    <label><input type="checkbox" value="0" class="checkboxes check_all" onchange="SelectCheckBox(this);"/> Отметить все &nbsp;</label>
                    <br/>Всего записей: <{$arrPageData.total_items}>
                </td>
                <td width="155">
                    <div class="dropDown" style="display:none;">
                        C отмеченными
                        <ul>
                            <li data-val="publish" onclick="$(this).parent().parent().find('input').val($(this).data('val')); $(this).closest('form').submit();">
                                <img src="/images/operation/check.png"/>&nbsp;&nbsp;опубликовать
                            </li>
                            <li data-val="unpublish" onclick="$(this).parent().parent().find('input').val($(this).data('val')); $(this).closest('form').submit();">
                                <img src="/images/operation/un_check.png"/>&nbsp;&nbsp;не публиковать
                            </li>
                            <li data-val="delete" onclick="$(this).parent().parent().find('input').val($(this).data('val'));$(this).closest('form').submit();">
                                <img src="/images/operation/delete.png"/>&nbsp;&nbsp;удалить
                            </li>
                        </ul>
                        <input type="hidden" name="allitems" value=""/>
                    </div>
                </td>
            <{else}>
                <td colspan="2"></td>
            <{/if}>
            <td align="right">
                <input name="submit_order" class="buttons" type="submit" value="Применить сортировку и цену" style="padding:0 10px;margin-right:65px"/>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript"> 
    $(function() {
        $('.sortable-table').find('td').each(function(){
            $(this).css('width', $(this).width() +'px');
        });
        $('.sortable-table').sortable({
            axis: 'y',
           // connectWith: ".sortable-tr",
            handle: ".sort-link",
            appendTo: "parent",
            //items: 'tr'
            
        }).disableSelection();
    
        $('#categorySearch').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '/interactive/ajax.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        zone: 'admin',
                        action: 'liveSearch',
                        modelID: '<{$arrPageData.modelID}>',
                        module: '<{$arrPageData.module}>',
                        searchStr: request.term
                    }, 
                    success: function(json) {
                        response($.map(json.items, function(item) {
                            return {
                                label: item.title,
                                value: item.title,
                                category: item.ctitle
                            }
                        }));
                    }
                });
            },
            select: function(event, ui) {},
            minLength: 2
        });
    });
</script>
<{/if}>
</div>
