<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.PRINT_TYPES creat_title=$smarty.const.ADMIN_CREATING_NEW_PRINT_TYPE edit_title=$smarty.const.ADMIN_EDIT_PRINT_TYPES}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<script type="text/javascript">
    function formCheck(form){
        if (form.title.value.length == 0) {
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        } return true;
    }
</script>
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' OR $arrPageData.task=='editItem'}>
    <form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
        <input type="hidden" name="order" value="<{$item.order}>"/>
        <input type="hidden" name="created" value="<{$item.created}>"/>
        <div class="tabsContainer">
            <ul class="nav">
                <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
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
                        <tr>
                            <td id="headb" align="left"><{$smarty.const.HEAD_TITLE_REDIRECT}></td>
                            <td>
                                <table border="0" cellspacing="0" cellpadding="0" class="sheet">
                                    <tr>
                                        <td align="left"><{$smarty.const.HEAD_REDIRECT_LINK}></td>
                                        <td align="center">или</td>
                                        <td align="center"><{$smarty.const.HEAD_EXTERNAL_LINK}></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="field" name="redirectid" onchange="itemsShowHide(this.form);" style="width: 320px;" <{if !empty($item.redirecturl)}>disabled<{/if}>>
                                                <option value="">- - <{$smarty.const.HEAD_SELECT_REDIRECT_LINK}> - -</option>
<{section name=i loop=$categoryTree}>
<{if !empty($categoryTree[i].categories)}>
                                                <optgroup label="<{$categoryTree[i].menutitle}>">
                                                    <{include file='common/tree_redirects.tpl' arItems=$categoryTree[i].categories selID=$item.redirectid marginLevel=0}>
                                                </optgroup>
<{/if}>
<{/section}>
                                            </select>
                                        </td>
                                        <td align="center">
                                            <input id="redirectype" name="redirectype" onchange="itemsShowHide(this.form);" type="checkbox" value="1" class="field" onclick="manageSelections(this, this.form.redirectid, this.form.redirecturl);" <{if !empty($item.redirecturl)}> checked<{/if}> />
                                        </td>
                                        <td align="center">
                                            <input id="redirecturl" name="redirecturl" type="text" size="55" value="<{$item.redirecturl}>"  class="field" <{if empty($item.redirecturl)}> disabled<{/if}> />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Мин. кол-во</td>
                            <td align="left">
                                от <input name="min_qty" id="min_qty" size="5" type="text" value="<{$item.min_qty}>"/> шт
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
                                <textarea style="width:<{if !empty($categoryTree)}>640<{else}>840<{/if}>px; height: 100px;" id="description" name="descr" ><{$item.descr}></textarea>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong><{$smarty.const.HEAD_CONTENT}></strong>
                                <a href="javascript:toggleEditor('fulldescription');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                                <textarea style="width:<{if !empty($categoryTree)}>640<{else}>840<{/if}>px; height: 500px;" id="fulldescription" name="fulldescr" ><{$item.fulldescr}></textarea>
                            </td>
                            <td class="buttons_row"></td>
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
        function itemsShowHide(f) {
            var display = '';
            if (f.redirectid.value.length > 0 || f.redirecturl.value.length > 0 || f.redirectype.checked)
                display = 'none';
            var bts = new Array('menuContent', 'menuImage', 'menuConfig', 'menuMeta', 'menuSEO');
            if (bts.length > 0){
                for (var i = 0; i < bts.length; i++) {
                   $('#'+bts[i]).closest('tr').css('display', display);
                }
            }
        }
    </script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    <{include file='common/new_page_btn.tpl' title=$smarty.const.HEAD_LINK_ADD_PRINT_TYPE}>
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
                <td align="center" valign="center" style="height:30px; width: 30px;"><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><img style="max-width:30px; max-height:30px;" src="<{if $items[i].image}><{$arrPageData.files_url|cat:$items[i].image}><{else}><{$arrPageData.files_url|cat:'noimage.jpg'}><{/if}>" /></a></td>
                <td>
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a>
                </td>
                <td align="center">
                    <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
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