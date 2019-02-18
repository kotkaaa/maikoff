<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title="Переадресации" creat_title="Добавить переадресацию" edit_title="Редактировать переадресацию"}>

<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
    <form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
        <input type="hidden" name="created" value="<{$item.created}>"/>
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
                            <td id="headb" align="left">Ссылка с <font style="color:red">*</font></td>
                            <td>
                                <input class="left" name="urifrom" size="70" id="urifrom" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.urifrom}>" />
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Ссылка на <font style="color:red">*</font></td>
                            <td>
                                <input class="left" name="urito" size="70" id="urito" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.urito}>" />
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
                    </table>
                </li>
                <li id="tab_history">
                    <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
                </li>
            </ul>
        </div>
    </form>
                    
    <script type="text/javascript">
        function formCheck(form){
            if (form.urifrom.value.length == 0) {
               alert('Введите ссылку с'); 
               return false;
            }
            if (form.urito.value.length == 0) {
               alert('Введите ссылку на');
               return false;
            } return true;
        }
    </script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    <{include file='common/new_page_btn.tpl' title="Добавить переадресацию"}>
    <div class="clear"></div>
    <form method="POST" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored">
            <tr>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="left" width="150">Ссылка с</td>
                <td id="headb" align="left" >Ссылка на</td>
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
                <td>
                    <a href="<{$items[i].urifrom}>"><{$items[i].urifrom}></a>
                </td>
                <td>
                    <a href="<{$items[i].urito}>"><{$items[i].urito}></a>
                </td>
                <td align="center">
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
                <td align="center" width="350">
<{if $arrPageData.total_pages>1}>
                    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
                </td>
            </tr>
        </table>
    </form>

<{*
<form method="post" action="<{$arrPageData.current_url|cat:"&task=import"}>" enctype="multipart/form-data">
    <fieldset style="border:1px solid #888;padding:10px;">
        <legend><b style="font-size:12px;">&nbsp;Импорт переадресаций&nbsp;</b></legend>
        <table>
            <tr>
                <td><input type="file" name="filename"/></td>
                <td><input class="buttons" type="submit" name="submit_add" value="Добавить переадресации"/></td>
                <td><input class="buttons" type="submit" name="submit_update" value="Обновить переадресации"/></td>
                <td><input class="buttons" type="button" onclick="location.href='<{$arrPageData.current_url|cat:"&task=deleteall"}>'" value="Удалить все переадресации"/></td>
            </tr>
            <tr>
                <td></td>
                <td><small>данные добавляются</small></td>
                <td><small>данные перезаписываютcя</small></td>
            </tr>
        </table>
        <p style="height:auto;margin:0;overflow:visible;">
            <font color="red">Внимание!</font><br/>
            Поддерживаются файлы в форматах <b>.xls</b> и <b>.xlsx</b>.<br/>
            Файл должен состоять из 2х колонок: 1 колонка - исходная ссылка, 2 колонка - ссылка на которую редиректить.<br/>
            1 ряд для заголовков колонок, он всегда пропускается скриптом.
        </p>
    </fieldset>
</form>
*}>

<{/if}>
</div>