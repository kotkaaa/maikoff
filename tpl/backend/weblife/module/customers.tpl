<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.USERS_TITLE creat_title=$smarty.const.ADMIN_CREATING_NEW_USER edit_title=$smarty.const.ADMIN_EDIT_USER}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<div id="right_block">
    <{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
    <{if $arrPageData.task=='addItem' or $arrPageData.task=='editItem'}>
        <form method="post" action="<{$arrPageData.current_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
            <div class="tabsContainer">
                <ul class="nav">
                    <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
                    <li><a href="javascript:void(0);" data-target="orders">Заказы</a></li>
                    <li><a href="javascript:void(0);" data-target="history">История</a></li>
                </ul>
                <div class="tab_line"></div>
                <ul class="tabs">
                    <li class="active" id="tab_main">            
                        <table border="1" cellspacing="0" cellpadding="1" class="sheet">       
                            <tr>
                                <td colspan="2">
                                    <strong>Данные пользователя: </strong>
                                </td>                                                                
                                <td rowspan="0" class="buttons_row" valign="top" width="144">
                                    <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                    <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=1}>
                                    <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" style="padding: 15px"> 
                                    <div class="inline"><{$smarty.const.USERS_FNAME}>: <span class="required">*</span> </div>
                                    <input class="field requirefield" name="firstname" size="80" type="text" value="<{$item.firstname}>" /><br/><br/>
                                    
                                    <div class="inline"><{$smarty.const.USERS_SNAME}>:</div>
                                    <input class="field" name="surname" size="80" type="text" value="<{$item.surname}>" /><br/><br/>
                                    
                                    <div class="inline"><{$smarty.const.USERS_MNAME}>: </div>
                                    <input class="field" name="middlename" type="text" size="80" value="<{$item.middlename}>" /><br/><br/>
                                                                        
                                    <div><{$smarty.const.USERS_DESCR}>: </div><br/>
                                    <textarea name="descr" id="descr" style="width:580px; height:60px;" class="field"><{$item.descr}></textarea><br/><br/>                                                                       
                                </td>
                                <td valign="top" style="padding: 15px">
                                    <div class="inline"><{$smarty.const.USERS_PHONE}><span class="required">*</span>: </div>
                                    <input class="field requirefield" name="phone" type="text" size="80" value="<{$item.phone}>" placeholder="0XXXXXXXXX"/><br/><br/>

                                    <div class="inline"><{$smarty.const.USERS_MAIL}>: </div>
                                    <input class="field" name="email" size="80" type="text" value="<{$item.email}>" />
                                    <input name="old_email" type="hidden" value="<{$item.email}>" /><br/><br/>

                                    <div class="inline">Город: </div>
                                    <input class="field" name="city" type="text" size="80" value="<{$item.city}>" /><br/><br/>
                                    
                                    <div class="inline"><{$smarty.const.LABEL_ADDRESS}>: </div>
                                    <input class="field" name="address" type="text" size="80" value="<{$item.address}>" /><br/><br/>
                                    
                                    <div class="inline">Забанен как спамер: </div>
                                    <label><input type="radio" name="banned" value="1" <{if $item.banned==1}>checked<{/if}>/> <{$smarty.const.OPTION_YES}></label>&nbsp;&nbsp;&nbsp;
                                    <label><input type="radio" name="banned" value="0" <{if $item.banned==0}>checked<{/if}>/> <{$smarty.const.OPTION_NO}></label><br/><br/>                                
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </li>
                    <li id="tab_orders">
                        <{include file="common/orders_table.tpl" orders=$item.orders}>
                    </li>
                    <li id="tab_history">
                        <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
                    </li>
                </ul>
            </div>
        </form>
        <script type="text/javascript">
            $(function() {
                $('form').find("input[name=\"phone\"]").inputmask({
                    mask: "0999999999",
                    greedy: false,
                    definitions: {
                        '*': {
                            validator: "[0-9]",
                            cardinality: 1,
                            casing: "lower"
                        }
                    }
                });
            });
            
            function formCheck(form){
                var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
                var regExpPhone = new RegExp("^([0-9 \-]{7,17})$");
                var errors = 0;
                $.each($(form).find('.requirefield'), function(i, input) {
                    if (input.value.length === 0 || (input.name === 'email' && input.value.match(regExpEmail) === null) || (input.name === 'phone' && input.value.match(regExpPhone) === null)){
                        if (!errors) $(this).focus();
                        $(this).addClass('error');
                        errors++;
                    } else {
                        $(this).removeClass('error');
                    }
                });
                if (errors > 0){    
                    alert("<{$smarty.const.USERS_EMPTY_FIELDS}>");
                    return false;
                } else {
                    return true;
                }
            }
        </script>
    <{else}>  
        <{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_USER}>
        <div class="search_form" style="margin-top: -5px;">
            <form action="<{$arrPageData.admin_url}>" method="GET">
                <input type="hidden" value="<{$arrPageData.module}>" name="module" />
                <input size="100" class="field autocomplete" type="text" name="arFilters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" placeholder="имя/фамилия/телефон/емейл"/>
                <input class="buttons inline-block" type="submit" value="Поиск" />
                <a style="margin:5px 10px;" href="<{$arrPageData.admin_url}>" title="Очистить">Сбросить</a>
            </form>
        </div>
        <div class="clear"></div>

        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored">
            <tr>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="left"><{$smarty.const.USERS_FULL_NAME}></td>
                <td id="headb" align="center" width="150">Телефон</td>
                <td id="headb" align="center" width="150"><{$smarty.const.USERS_MAIL}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
            <{section name=i loop=$items}>
                <tr>
                    <td align="center"><{$items[i].id}></td>
                    <td>
                        <a href="<{$arrPageData.current_url|cat:"&task=viewItem&itemID="|cat:$items[i].id}>" title="Просмотр">
                            <{$items[i].surname}> <{$items[i].firstname}> <{$items[i].middlename}>
                        </a>
                    </td>
                    <td align="center"><{if $items[i].phone}><{$items[i].phone}><{else}> -- <{/if}></td>
                    <td align="center"><{if $items[i].email}><{$items[i].email}><{else}> -- <{/if}></td>
                    <td align='center'>
                        <a href="<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                            <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                        </a>
                    </td>
                    <td align='center'>
                        <a href="<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
                            <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" />
                        </a>
                    </td>
                </tr>
            <{/section}>
        </table>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr>
                <td align="center" width="345"></td>
                <td align="center" width="350">
                    <{if $arrPageData.total_pages>1}>
                        <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                        <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{/if}>
                </td>
                <td align="right">

                </td>
            </tr>
        </table>
                
        <script type="text/javascript">
            $(function() {
                $('.autocomplete').autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: '/interactive/ajax.php',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                zone: 'admin',
                                action: 'liveSearch',
                                module: '<{$arrPageData.module}>',
                                searchStr: request.term
                            }, 
                            success: function(json) {
                                response($.map(json.items, function(item) {
                                    return {
                                        label: item.title,
                                        value: item.title
                                    }
                                }));
                            }
                        });
                    },
                    select: function(event, ui) {
                    
                    },
                    minLength: 2
                });
            });
        </script>
    <{/if}>
</div>