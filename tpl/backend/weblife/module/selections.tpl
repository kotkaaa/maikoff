<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.SELECTIONS creat_title=$smarty.const.ADMIN_CREATING_NEW_SELECTION edit_title=$smarty.const.ADMIN_EDIT_SELECTION}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' or $arrPageData.task=='editItem'}>
    <form method="POST" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
        <input type="hidden" name="createdDate" value="<{$item.createdDate}>"/>
        <input type="hidden" name="createdTime" value="<{$item.createdTime}>"/>
        <input type="hidden" name="type" value="<{$item.type}>"/>
        <div class="tabsContainer">
            <ul class="nav">
                <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
                <li><a href="javascript:void(0);" data-target="history">История</a></li>
            </ul>
            <div class="tab_line"></div>
            <ul class="tabs">
                <li class="active" id="tab_main" style="min-height: 600px;">
                    <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                        <tr>
                            <td id="headb" align="left"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                            <td>
                                <input class="left" id="title" name="title" size="100" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.title}>" />
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0 disableCopy=1 disableDelete=1}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Второй заголовок <font style="color:red">*</font></td>
                            <td>
                                <input class="left" id="descr" name="descr" size="100" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.descr}>" /> 
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center"></td>
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
                        <tr>
                            <td id="headb" align="left">Тип</td>
                            <td>
                                <{if $item.type == Selections::SELECTION_TYPE_CUSTOM}>Изображения<{else}>Товары<{/if}>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center"></td>
                        </tr>                        
<{if $item.type == Selections::SELECTION_TYPE_CUSTOM}>
                        <tr>
                            <td id="headb" align="left">Товары</td>
                            <td>
                                <a class="buttons left" 
                                   href="/admin.php?module=selection_files_uploadify&ajax=1&selectionID=<{$item.id}>" 
                                   onclick="return hs.htmlExpand(this, {headingText:'Управление файлами', objectType:'iframe', preserveContent: false, width:1024});">
                                    Изображения
                                </a> 
                                <div class="left" style="margin-left:10px;margin-top:3px;line-height:30px">загружено <{$item.imagesCount}></div>
                                <div class="clear"></div>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
<{else}>
                        <tr>
                            <td id="headb" align="left" valign="top">Товары</td>
                            <td>
                                <input id="productSearch" type="text" placeholder="код/название принта" size="100"/><br/><br/>
                                <h4>Товары:</h4>
                                <table id="products" class="list colored" style="width:530px">
                                    <thead>
                                        <td id="headb" style="width:200px">название</td>
                                        <td id="headb" style="width:200px">категория</td>
                                        <td id="headb" style="width:40px" align="center">сорт</td>
                                        <td id="headb" style="width:40px" align="center">удал</td>
                                    </thead>
                                    <tbody class='sortable-table'>
<{section name=i loop=$item.arProducts}>
                                        <tr class="sortable-tr">                                            
                                            <td>
                                                <input type="hidden" name="arProducts[]" value="<{$item.arProducts[i].product_id}>"/>
                                                <{$item.arProducts[i].title}>
                                            </td>
                                            <td><{$item.arProducts[i].category_title}></td>
                                            <td align="center"><img class="sort-link" src="/images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" style='cursor:move'/></td>
                                            <td align="center"><a href="javascript:;" onclick="$(this).closest('tr').remove();"><img src="<{$arrPageData.system_images}>delete.png"/></a></td>
                                        </tr>
<{/section}>
                                    </tbody>
                                </table>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center"></td>
                        </tr>                      
<{/if}>
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
            if(form.title.value.length == 0){
               alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>');
               return false;
            } 
            if(form.descr.value.length == 0){
               alert('Вы не ввели второе название');
               return false;
            } 
            return true;
        }
        
        $(function() {
            $('.sortable-table').find('td').each(function(){
                $(this).css('width', $(this).width() +'px');
            });
            $('.sortable-table').sortable({
                axis: 'y',
                handle: ".sort-link",
                appendTo: "parent"
            }).disableSelection();
        
            $('#productSearch').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '/interactive/ajax.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            zone: 'admin',
                            action: 'liveSearch',
                            module: 'prints',
                            searchStr: request.term
                        }, 
                        success: function(json) {
                            response($.map(json.items, function(item) {
                                return {
                                    label: item.title,
                                    value: item.title,
                                    id: item.id,
                                    category: item.ctitle
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    var idx = [];
                    if($('[name="arProducts[]"]').length>0) {
                        $.each($('[name="arProducts[]"]'), function() {
                            idx.push($(this).val());
                        }); 
                    }
                    if($.inArray(ui.item.id, idx) == -1) {
                        var html = '<tr><td><input type="hidden" name="arProducts[]" value="'+ui.item.id+'"/>'+ui.item.label+' (несохранено)</td><td>'+ui.item.category+'</td>'+
                                   '<td align="center"><img class="sort-link" src="/images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" style="cursor:move"/></td>'+
                                   '<td align="center"><a href="javascript:;" onclick="$(this).closest(\'tr\').remove();"><img src="<{$arrPageData.system_images}>delete.png"/></a></td></tr>';
                        $('#products').find('tbody').append(html);
                    } else alert('Товар '+ui.item.label+' уже добавлен!');
                    $(this).val(''); 
                    return false;
                },
                minLength: 2
            });
        });        
    </script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    <form method="POST" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored">
            <tr>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                <td id="headb" align="center" >Второе название</td>
                <td id="headb" align="center" width="300">Описание (где выводится)</td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
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
                <td align="left">
                    <a href="<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a>
                </td>
                <td align="left">
                    <{$items[i].descr}>
                </td>
                <td align="left">
                    <{Selections::$SELECTIONS[$items[i].alias]}>
                </td>
                <td align="center" >
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                        <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                    </a>
                </td>
            </tr>
<{/section}>
        </table>
    </form>
<{/if}>
</div>