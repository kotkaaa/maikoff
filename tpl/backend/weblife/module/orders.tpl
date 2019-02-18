<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.ORDERS creat_title=$smarty.const.ADMIN_CREATING_NEW_ORDER edit_title=$smarty.const.ADMIN_EDIT_ORDER}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block" style="position:relative">
<{* +++++++++++++++++ SHOW EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<div style="position:absolute;right:10px;z-index:9999">
    <a href="/admin.php?module=orders<{$arrPageData.filter_url}>"><b>Вернуться назад</b></a>
</div>
<div class="tabsContainer">
    <ul class="nav">
        <li><a href="javascript:void(0);" data-target="main" class="active">Информация о заказе</a></li>
    </ul>
    <div class="tab_line"></div>
    <ul class="tabs">
        <li class="active" id="tab_main">
            <table border="0" cellspacing="5" cellpadding="1" class="sheet" id="orderTable" style="border-bottom:1px solid #b8b8b8">  
                <tr>
                    <td class="orderEditForm" width="400" style="border-right:1px solid #b8b8b8" valign="top">
                        <form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return Order.checkForm();">
                            <strong>Информация о клиенте</strong><br/><br/>
                            <table width="100%" border="1" cellspacing="1" cellpadding="1" class="list colored" style="text-align: left;">
                                <tbody>
                                    <tr>
                                        <td width="120">Клиент</td>
                                        <td>
                                            <input type="hidden" name="user_id" value="<{$item.user_id}>"/>
                                            <div class="userInfo" style="padding-bottom:5px;">
                                                <{if $item.user_id}>
                                                    <div class="inline-block" style="width:220px;vertical-align:middle" >
                                                        <a href="/admin.php?module=customers&task=editItem&itemID=<{$item.user_id}>" target="_blank">
                                                            <{$item.user_title}><br/><{$item.user_phone}>
                                                        </a>
                                                    </div>
                                                <{/if}>
                                            </div>
                                            <div class="clientSearch<{if $item.user_id}> hidden_block<{/if}>" style="padding-bottom:5px;">
                                                <div style="padding-bottom:10px">Найдите клиента по номеру телефона или заполните данные для создания нового</div>
                                                <input type="text" id="userSearch" size="47" value="" placeholder="подбор по телефону/емейлу/имени"/> 
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="120">Имя<font color="red">*</font></td>
                                        <td class="value">
                                            <{if $item.name}><{$item.name}><{else if $item.user_id}><{$item.user_title}><{/if}>
                                            <{if $item.recepient}><br/>(получатель НП <{$item.recepient}>)<{/if}>
                                        </td>
                                        <td class="editable hidden_block">
                                            <input class="required" type="text" size="47" name="name" placeholder="Введите имя" value="<{if $item.name}><{$item.name}><{else if $item.user_id}><{$item.user_title}><{/if}>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телефон<font color="red">*</font></td>
                                        <td class="value"><{$item.phone}></td>
                                        <td class="editable hidden_block">
                                            <input class="required" type="text" size="47" name="phone" placeholder="Введите номер телефона" value="<{$item.phone}>"/>
                                        </td>
                                    </tr>  
                                    <tr>
                                        <td>E-mail</td>
                                        <td class="value"><{if $item.email}><{$item.email}><{else}> -- <{/if}></td>
                                        <td class="editable hidden_block">
                                            <input type="text" size="47" name="email" placeholder="Введите емейл" value="<{$item.email}>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Город</td>
                                        <td class="value"><{if $item.city}><{$item.city}><{else}> -- <{/if}></td>
                                        <td class="editable hidden_block">
                                            <input type="text" size="47" name="city" placeholder="Введите город" value="<{$item.city}>"/>
                                        </td>
                                    </tr>  
                                    <tr>
                                        <td>Адрес</td>
                                        <td class="value"><{if $item.address}><{$item.address}><{else}> -- <{/if}></td>
                                        <td class="editable hidden_block">
                                            <textarea name="address" style="width:255px;height:40px" placeholder="Введите адрес"><{$item.address}></textarea>
                                        </td>
                                    </tr>  
                                    <tr>
                                        <td>Описание клиента</td>
                                        <td><span class="descr"><{if $item.descr}><{$item.descr}><{else}> -- <{/if}></span></td>
                                    </tr>
                                    <tr<{if $arrPageData.task == 'addItem'}> class="hidden_block"<{/if}>>
                                        <td>Комментарий клиента к заказу</td>
                                        <td><{if $item.comment}><{$item.comment}><{else}> -- <{/if}></td>
                                    </tr>
                                </tbody>
                            </table>
                                    
                            <br/><br/><strong>Информация о заказе</strong><br/><br/>
                            <table width="100%" border="1" cellspacing="1" cellpadding="1" class="list colored" style="text-align: left;">
                                <tbody>
                                    <tr<{if $arrPageData.task == 'addItem'}> class="hidden_block"<{/if}>>
                                        <td width="120">№ заказа</td>
                                        <td><strong><{if $item.id}><{$item.id}><{else}> -- <{/if}></strong></td>
                                    </tr>
                                    <tr<{if $arrPageData.task == 'addItem'}> class="hidden_block"<{/if}>>
                                        <td>Канал</td>
                                        <td><{if $item.channel}><{$item.channel}><{else}> -- <{/if}></td>
                                    </tr>
                                    <tr>
                                        <td>Создан</td>
                                        <td><strong><{HTMLHelper::formatDate($item.created)}></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Запланирован</td>
                                        <td class="value"><{if $item.planned}><strong><{HTMLHelper::formatDate($item.planned, true)}></strong><{else}> -- <{/if}></td>
                                        <td class="editable hidden_block">
                                            <input id="planned" class="datepicker" type="text" size="25" name="planned" placeholder="Выберите дату" value="<{if $item.planned}><{HTMLHelper::formatDate($item.planned, true)}><{/if}>"/>
                                        </td>
                                    </tr>
                                    <tr<{if $arrPageData.task == 'addItem'}> class="hidden_block"<{/if}>>
                                        <td>Выполнен</td>
                                        <td><{if $item.closed}><strong><{HTMLHelper::formatDate($item.closed)}></strong><{else}> -- <{/if}></td>
                                    </tr> 
                                    <tr>
                                        <td>Менеджер<font color="red">*</font></td>
                                        <td class="value"><{$item.manager_title}></td>
                                        <td class="editable hidden_block">
                                            <select autocomplete="off" name="manager_id" class="required" style="width:150px;"<{if $arrPageData.task == 'addItem'}> disabled<{/if}>>
                                            <{section name=i loop=$arrPageData.arManagers}>
                                                <option value="<{$arrPageData.arManagers[i].id}>"<{if $arrPageData.arManagers[i].id==$item.manager_id || ($item.manager_id == 0 && $arrPageData.arManagers[i].id==$objUserInfo->id) }> selected<{/if}>>
                                                    <{$arrPageData.arManagers[i].firstname}> <{$arrPageData.arManagers[i].surname}>
                                                </option>
                                            <{/section}>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Статус<font color="red">*</font></td>
                                        <td class="value"><{$item.status_title}></td>
                                        <td class="editable hidden_block">
                                            <select autocomplete="off" name="status_id" class="required" onchange="Order.toogleStatus($(this).val());" style="width:150px;"<{if $arrPageData.task == 'addItem'}> disabled<{/if}>>
                                            <{section name=i loop=$arrPageData.arStatuses}>
                                                <option value="<{$arrPageData.arStatuses[i].id}>"<{if $arrPageData.arStatuses[i].id==$item.status_id}> selected<{else if !in_array($arrPageData.arStatuses[i].id, $arrPageData.availableStatuses)}> disabled<{/if}>>
                                                    <{$arrPageData.arStatuses[i].title}>
                                                </option>
                                            <{/section}>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Оплата<font color="red">*</font></td>
                                        <td class="value" valign="middle">
                                            <{$item.payment_title}>
                                        </td>
                                        <td class="editable hidden_block">
                                            <select autocomplete="off" name="payment_id" class="required" style="width:150px;">
                                                <option value="">Выберите оплату</option>
                                            <{section name=i loop=$arrPageData.arPayments}>
                                                <option value="<{$arrPageData.arPayments[i].id}>"<{if $arrPageData.arPayments[i].id==$item.payment_id}> selected<{/if}>>
                                                    <{$arrPageData.arPayments[i].title}>
                                                </option>
                                            <{/section}>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Доставка<font color="red">*</font></td>
                                        <td class="value" valign="middle">
                                            <{$item.shipping_title}>
                                            <{if $item.shipping_id == OrderHelper::DELIVERY_TYPE_POST && $item.track_code}>
                                                <br/>НН: <{$item.track_code}> 
                                            <{/if}>
                                        </td>
                                        <td class="editable hidden_block">
                                            <select autocomplete="off" name="shipping_id" class="required" style="width:150px;" onchange="Order.toggleDeliveryInfo($(this).val());">
                                                <option value="">Выберите доставку</option>
                                            <{section name=i loop=$arrPageData.arShippings}>
                                                <option value="<{$arrPageData.arShippings[i].id}>"<{if $arrPageData.arShippings[i].id==$item.shipping_id}> selected<{/if}> data-price="<{$arrPageData.arShippings[i].price}>">
                                                    <{$arrPageData.arShippings[i].title}>
                                                </option>
                                            <{/section}>
                                            </select>
                                            <div class="delivery_info delivery_<{OrderHelper::DELIVERY_TYPE_POST}><{if $item.shipping_id != OrderHelper::DELIVERY_TYPE_POST}> hidden_block<{/if}>" style="margin-top:10px;line-height:27px;">
                                                <input type="text" size="25" class="left" name="track_code" value="<{$item.track_code}>" placeholder="номер декларации"/>
                                            </div>
                                            <div class="delivery_info delivery_<{OrderHelper::DELIVERY_TYPE_COURIER}><{if $item.shipping_id != OrderHelper::DELIVERY_TYPE_COURIER}> hidden_block<{/if}>" style="margin-top:10px;line-height:27px;">
                                                <input type="text" size="7" class="left" name="shipping_price" value="<{$item.shipping_price}>" placeholder="стоимость доставки"/> &nbsp; грн
                                            </div>
                                        </td>
                                    </tr>
                                    <{if OrderHelper::isRequest($item.type_id)}>
                                    <tr>
                                        <td>Стоимость товаров</td>
                                        <td class="value"><{$item.total_price}></td>
                                        <td class="editable hidden_block">
                                            <input type="text" size="7" class="left" name="total_price" value="<{$item.total_price}>" placeholder="стоимость товаров"/>
                                        </td>
                                    </tr>
                                    <{/if}>
                                    <tr>
                                        <td>Сумма предоплаты</td>
                                        <td class="value"><{$item.prepay}></td>
                                        <td class="editable hidden_block">
                                            <input type="text" size="7" class="left" name="prepay" value="<{$item.prepay}>" placeholder="сумма предоплаты"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Комментарий админа к заказу</td>
                                        <td class="value"><{$item.admin_comment}></td>
                                        <td class="editable hidden_block">
                                            <textarea name="admin_comment" style="width:250px;height:40px" placeholder="Введите комментарий"><{$item.admin_comment}></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <{if $arrPageData.isEditable}>
                            <br/>   
                            <input type="button" class="buttons right open_edit" value="Редактировать" onclick="Order.openEditForm();"/>
                            <div class="edit_buttons hidden_block right">
                                <{if $arrPageData.task == 'editItem'}>
                                <input type="button" class="buttons inline-block" value="Отмена" onclick="Order.closeEditForm();"/> &nbsp; 
                                <{/if}>
                                <input type="button" class="buttons inline-block" value="<{if $arrPageData.task == 'editItem'}>Сохранить<{else}>Создать<{/if}>" onclick="Order.saveEditForm();"/>                            
                            </div>
                            <{/if}>
                        </form>
                    </td>
                    <td valign="top">
                        <{if $arrPageData.task == 'editItem'}>
                        <strong>Товары</strong><br/><br/>                        
                        <{include file="ajax/order_products.tpl"}>                               
                        <table width="100%">
                            <tr>
                                <td>
                                    <{if $arrPageData.isEditableProducts && !OrderHelper::isRequest($item.type_id)}>
                                    <a href="/admin.php?module=order_product&task=addItem&orderID=<{$item.id}>&ajax=1" class="buttons" onclick="return hs.htmlExpand(this, {headingText:'Добавление товара', objectType:'iframe', preserveContent: false, width:800})">
                                        Добавить принт
                                    </a>
                                    <{/if}>
                                </td>
                                <td align="right">
                                    <a href="/admin.php?module=print&itemID=<{$item.id}>&itemModule=<{$arrPageData.module}>&ajax=1" class="buttons" onclick="return hs.htmlExpand(this, {headingText:'Печать чека', objectType:'iframe', preserveContent: false, width:600, height:650})">
                                        Печать чека
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <{if $item.status_id != OrderHelper::STATUS_DONE && $item.status_id != OrderHelper::STATUS_CANCEL}>
                        <div style="border:1px solid #b8b8b8;padding:5px;margin:5px 0">
                        <table>
                            <tr>
                                <td align="center" width="200" valign="top">
                                    <textarea style="width:100%;height:60px">Ваш заказ №<{$item.id}> принят в обработку. Maikoff.com.ua</textarea><br/>
                                    <input type="button" class="buttons" value="SMS номер заказа" onclick="Order.sendSMS(this, 'order_id');"/>
                                    <div class="sms_sended"><{if $item.sms_order_id}>отправлено <{$item.sms_order_id|date_format:"d.m.Y H:i:s"}><{/if}></div>
                                </td>
                            <{if $item.prepay>0}>
                                <td align="center" width="200" valign="top">
                                    <textarea style="width:100%;height:60px">Предоплата <{HTMLHelper::formatPrice($item.prepay)}>грн, карта 5168 7555 2359 3693, Костерная О.В. Maikoff.com.ua</textarea><br/>
                                    <input type="button" class="buttons" value="SMS предоплата" onclick="Order.sendSMS(this, 'prepay');"/>
                                    <div class="sms_sended"><{if $item.sms_prepay}>отправлено <{$item.sms_prepay|date_format:"d.m.Y H:i:s"}><{/if}></div>
                                </td>
                            <{/if}>
                            <{if $item.track_code}>
                                <td align="center" width="200" valign="top">
                                    <textarea style="width:100%;height:60px">Ваш заказ отправлен, НН<{$item.track_code}>. Maikoff.com.ua</textarea><br/>
                                    <input type="button" class="buttons" value="SMS номер накладной" onclick="Order.sendSMS(this, 'track_code');"/>
                                    <div class="sms_sended"><{if $item.sms_track_code}>отправлено <{$item.sms_track_code|date_format:"d.m.Y H:i:s"}><{/if}></div>
                                </td>
                            <{/if}>
                            </tr>
                        </table>
                        <{/if}>
                        </div>
<{if !empty($item.arFiles)}>    
                        <br/><h3>Загруженные файлы</h3><br/>
<{section name=i loop=$item.arFiles}>
                        <a href="<{$smarty.const.UPLOAD_ORDER_FILES_URL}><{$item.arFiles[i].filename}>" class="highslide inline-block mr-10" onclick="return hs.expand(this);">
                            <{$item.arFiles[i].filename}>
                        </a>
<{/section}>
<{/if}>
                        <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
                        <{else}>
                            <input type="button" class="buttons green inline-block" value="Создать заказ" onclick="Order.saveEditForm();" style="padding:20px;line-height:2px;font-size:17px;"/>
                        <{/if}>
                    </td> 
                </tr>
            </table>
        </li>
    </ul>
</div>
                         
<script type="text/javascript">
    $(function() {     
        Order.init();
    });
    
    var Order = {
        wrapper: $('.orderEditForm'),
        
        init: function() {
            var self = this;
            
            initDatePickers();
            <{if $arrPageData.task == 'addItem'}>
                Order.openEditForm(true);
            <{/if}>
                
            hs.Expander.prototype.onAfterClose = function() {
                $.ajax({
                    url: '<{$arrPageData.admin_url}>&task=getProducts&itemID=<{$item.id}>',
                    dataType: 'json',
                    type: 'GET',
                    success: function (json) {
                        if(json.output) $('#orderProducts').replaceWith(json.output);
                        if(json.history) $('#object_history').replaceWith(json.history);
                    },
                });
            };
                
            $(self.wrapper).find('form').find('[name="phone"]').inputmask({
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

            $('#userSearch').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '/interactive/ajax.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            zone: 'admin',
                            action: 'liveSearch',
                            module: 'customers',
                            searchStr: request.term
                        }, 
                        success: function(json) {
                            response($.map(json.items, function(item) {
                                return {
                                    label: item.title,
                                    value: item.title,
                                    name: item.name,
                                    id: item.id,
                                    phone: item.phone,
                                    email: item.email,
                                    city: item.city,
                                    address: item.address,
                                    descr: item.descr,
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    var form = $(self.wrapper).find('form');
                    $(form).find('[name="user_id"]').val(ui.item.id);
                    $(form).find('[name="name"]').val(ui.item.name);
                    $(form).find('[name="phone"]').val(ui.item.phone);
                    $(form).find('[name="email"]').val(ui.item.email);
                    $(form).find('[name="city"]').val(ui.item.city);
                    $(form).find('[name="address"]').val(ui.item.address);
                    $(form).find('.descr').text(ui.item.descr);
                    $(form).find('.userInfo').html('<div class="inline-block" style="width:220px;vertical-align:middle">'+
                                                    '<a href="/admin.php?module=customers&task=editItem&itemID='+ui.item.id+'" target="_blank">'+
                                                    ui.item.name+'<br/>'+ui.item.phone+
                                                    '</a></div> &nbsp;&nbsp; <a href="javascript:;" '+
                                                    'class="inline-block" style="width:20px;vertical-align:middle" onclick="Order.clearClient();">'+
                                                    '<img src="<{$arrPageData.system_images}>delete.png"/></a>');
                    $(form).find('.clientSearch').addClass('hidden_block');
                    $(this).val("");
                    return false;
                },
                minLength: 2
            });
        },
        
        clearClient: function() {
            var self = this;
            if(confirm("Открепить пользователя и все его заполненные данные?")) {
                var form = $(self.wrapper).find('form');
                $(form).find('[name="user_id"]').val('');
                $(form).find('[name="name"]').val('');
                $(form).find('[name="phone"]').val('');
                $(form).find('[name="email"]').val('');
                $(form).find('[name="city"]').val('');
                $(form).find('[name="address"]').val('');
                $(form).find('.descr').text('');
                $(form).find('.userInfo').html('');
                $(form).find('.clientSearch').removeClass('hidden_block');
            }
        },
        
        saveEditForm: function() {
            var self = this;
            $(self.wrapper).find('form').submit();
        },
        
        checkForm: function() {
            var self = this;
            var errors = 0;
            
            $.each($(self.wrapper).find('.required'), function() {
                if($(this).val().length == 0) {
                    $(this).addClass('error');
                    errors++;
                } else {
                    $(this).removeClass('error');
                }
            });
            
            return (errors > 0 ? false : true);
        },
        
        openEditForm: function(focus) {
            var self = this;
            var focus = focus||false;
            $(self.wrapper).find('.open_edit, .value').addClass('hidden_block');            
            $(self.wrapper).find('.edit_buttons').removeClass('hidden_block');
            $(self.wrapper).find('.editable').removeClass('hidden_block');
            if(focus) {
                $('#userSearch').focus();
            }
        },
        
        closeEditForm: function() {
            var self = this;
            $(self.wrapper).find('.open_edit, .value').removeClass('hidden_block');
            $(self.wrapper).find('.edit_buttons').addClass('hidden_block');
            $(self.wrapper).find('.editable').addClass('hidden_block');
        },
        
        toggleDeliveryInfo: function(shipping) {
            $('.delivery_info').addClass('hidden_block');
            $('.delivery_info.delivery_'+shipping).removeClass('hidden_block');
            if(shipping == <{OrderHelper::DELIVERY_TYPE_COURIER}> && $('select[name="shipping_id"]').find('option:selected').data('price').length > 0) {
                $('.delivery_info.delivery_'+shipping).find('input[name="shipping_price"]').val($('select[name="shipping_id"]').find('option:selected').data('price'));
            }
        },
                
        toogleStatus: function(status) {
            if(status == <{OrderHelper::STATUS_CANCEL}>) {
                $('select[name="shipping_id"]').removeClass('required');
            } else $('select[name="shipping_id"]').addClass('required');
        },
        
        sendSMS: function(block, type) {
            var textInput = $(block).closest('td').find('textarea');
            if($(textInput).val().length>0) {
                $(textInput).removeClass('error');
                if(confirm('Отправить СМС?')) {
                    $.ajax({
                        url: '<{$arrPageData.admin_url}>&task=sendSMS&itemID=<{$item.id}>&type='+type+'&text='+$(textInput).val(),
                        type: 'GET',
                        dataType: 'json',
                        success: function(json){
                            if(json) {
                                if(json.message) {
                                    $('#messages').text(json.message).removeClass('hidden_block').addClass('info');
                                }
                                if(json.updated) $(block).closest('td').find('.sms_sended').html(json.updated);
                                if(json.history) $('#object_history').replaceWith(json.history);
                            }
                        }
                    }); 
                }
            } else {
                $(textInput).addClass('error');
                $(textInput).focus();
            }
        },
    };
</script>
                    
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<{include file='common/new_page_btn.tpl' title="Создать заказ&nbsp;&nbsp;"}>
<div class="clear"></div>

<div class="search_block stylized">
    <form method="GET" id="searchForm" action="">
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        
        <div class="row">
            <div class="inline">создан</div>
            <input type="text" class="datepicker datetimerange preset" name="" size="20" style="font-size:15px" data-name="created"
                   data-from="<{if isset($arrPageData.filters.created.from) && $arrPageData.filters.created.from}><{$arrPageData.filters.created.from}><{/if}>"
                   data-to="<{if isset($arrPageData.filters.created.to) && $arrPageData.filters.created.to}><{$arrPageData.filters.created.to}><{/if}>"/>
            <input type="hidden" id="created_from" name="filters[created][from]" 
                   value="<{if isset($arrPageData.filters.created.from) && $arrPageData.filters.created.from}><{$arrPageData.filters.created.from}><{/if}>"/>             
            <input type="hidden" id="created_to" name="filters[created][to]" 
                   value="<{if isset($arrPageData.filters.created.to) && $arrPageData.filters.created.to}><{$arrPageData.filters.created.to}><{/if}>"/> 
                        
            <div class="inline">менеджер</div>
            <select name="filters[manager_id]">
                <option value=""> -- не выбрано -- </option>
                <{section name=i loop=$arrPageData.arManagers}>
                    <option value="<{$arrPageData.arManagers[i].id}>"<{if isset($arrPageData.filters.manager_id) && $arrPageData.arManagers[i].id == $arrPageData.filters.manager_id}> selected<{/if}>>
                        <{$arrPageData.arManagers[i].firstname}> <{$arrPageData.arManagers[i].surname}>
                    </option>
                <{/section}>
            </select>
            
            <div class="inline">клиент</div>
            <select name="filters[user_id]" style="width:200px;">
                <option value=""> -- не выбрано -- </option>
                <{section name=i loop=$arrPageData.arClients}>
                    <option value="<{$arrPageData.arClients[i].id}>"<{if isset($arrPageData.filters.user_id) && $arrPageData.arClients[i].id == $arrPageData.filters.user_id}> selected<{/if}>>
                        <{$arrPageData.arClients[i].firstname}> <{$arrPageData.arClients[i].surname}> <{$arrPageData.arClients[i].phone}>
                    </option>
                <{/section}>
            </select>
            
            <div class="inline">канал</div>
            <select name="filters[channel_code]">
                <option value=""> -- не выбрано -- </option>
                <{foreach from=AdManager::$CLIENTS item=channel key=code}>
                    <option value="<{$code}>"<{if isset($arrPageData.filters.channel_code) && $code == $arrPageData.filters.channel_code}> selected<{/if}>>
                        <{$channel.name}>
                    </option>
                <{/foreach}>
            </select>
            
            <button type="submit" class="buttons inline-block" style="margin-left:25px;">Применить</button>
            
            &nbsp;&nbsp; <a href="<{$arrPageData.admin_url}>" style="margin-left:25px;">Сбросить</a>
        </div>
        
        <div class="row">
            <div class="inline">запланирован</div>
            <input type="text" class="datepicker datetimerange" name="" size="20" style="font-size:15px" data-name="planned"
                   data-from="<{if isset($arrPageData.filters.planned.from) && $arrPageData.filters.planned.from}><{$arrPageData.filters.planned.from}><{/if}>"
                   data-to="<{if isset($arrPageData.filters.planned.to) && $arrPageData.filters.planned.to}><{$arrPageData.filters.planned.to}><{/if}>"/>
            <input type="hidden" id="planned_from" name="filters[planned][from]" 
                   value="<{if isset($arrPageData.filters.planned.from) && $arrPageData.filters.planned.from}><{$arrPageData.filters.planned.from}><{/if}>"/>             
            <input type="hidden" id="planned_to" name="filters[planned][to]" 
                   value="<{if isset($arrPageData.filters.planned.to) && $arrPageData.filters.planned.to}><{$arrPageData.filters.planned.to}><{/if}>"/> 
            
            <div class="inline">доставка</div>
            <{section name=i loop=$arrPageData.arShippings}>
                <label>
                    <input type="checkbox" name="filters[shipping][]" value="<{$arrPageData.arShippings[i].id}>"<{if !empty($arrPageData.filters.shipping) && in_array($arrPageData.arShippings[i].id, $arrPageData.filters.shipping)}> checked<{/if}>/> 
                    <{$arrPageData.arShippings[i].title}>
                </label>
            <{/section}>
            
            <div class="inline">поиск</div>
            <input size="55" type="text" placeholder="поиск по номеру заказа/имени/телефону/емейлу" id="categorySearch" name="filters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" />
        </div>
        
        <div class="row">
            <div class="inline">выполнен</div>
            <input type="text" class="datepicker datetimerange" name="" size="20" style="font-size:15px" data-name="closed"
                   data-from="<{if isset($arrPageData.filters.closed.from) && $arrPageData.filters.closed.from}><{$arrPageData.filters.closed.from}><{/if}>"
                   data-to="<{if isset($arrPageData.filters.closed.to) && $arrPageData.filters.closed.to}><{$arrPageData.filters.closed.to}><{/if}>"/>
            <input type="hidden" id="closed_from" name="filters[closed][from]" 
                   value="<{if isset($arrPageData.filters.closed.from) && $arrPageData.filters.closed.from}><{$arrPageData.filters.closed.from}><{/if}>"/>             
            <input type="hidden" id="closed_to" name="filters[closed][to]" 
                   value="<{if isset($arrPageData.filters.closed.to) && $arrPageData.filters.closed.to}><{$arrPageData.filters.closed.to}><{/if}>"/> 
            
            <div class="inline">статус</div>
            <{section name=i loop=$arrPageData.arStatuses}>
                <label>
                    <input type="checkbox" name="filters[status][]" value="<{$arrPageData.arStatuses[i].id}>"<{if !empty($arrPageData.filters.status) && in_array($arrPageData.arStatuses[i].id, $arrPageData.filters.status)}> checked<{/if}>/> 
                    <{$arrPageData.arStatuses[i].title}>
                </label>
            <{/section}>            
        </div>
        
        <div class="row">
            <div class="inline">тип заказа</div>
            <{section name=i loop=$arrPageData.arTypes}>
                <label>
                    <input type="checkbox" name="filters[type][]" value="<{$arrPageData.arTypes[i].id}>"<{if !empty($arrPageData.filters.type) && in_array($arrPageData.arTypes[i].id, $arrPageData.filters.type)}> checked<{/if}>/> 
                    <{$arrPageData.arTypes[i].title}>
                </label>
            <{/section}>     
            
            <div class="inline">оплата</div>
            <{section name=i loop=$arrPageData.arPayments}>
                <label>
                    <input type="checkbox" name="filters[payment][]" value="<{$arrPageData.arPayments[i].id}>"<{if !empty($arrPageData.filters.payment) && in_array($arrPageData.arPayments[i].id, $arrPageData.filters.payment)}> checked<{/if}>/> 
                    <{$arrPageData.arPayments[i].title}>
                </label>
            <{/section}>  
        </div>
    </form>
</div>
<div class="clear"></div> 

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
    <tr>
        <td id="headb" align="center" width="50">№ заказа</td>
        <td id="headb" align="center" width="50">Тип</td>
        <td id="headb" align="center" width="50">Менеджер</td>            
        <td id="headb" align="center" width="50">Создан</td>
        <td id="headb" align="center" width="50">Заплани-<br/>рован</td>
        <td id="headb" align="center" width="50">Выполнен</td>  
        <td id="headb" align="center" width="110">Статус</td>            
        <td id="headb">Коммент менеджера</td>
        <td id="headb" align="center" width="50">Клиент</td>
        <td id="headb" align="center" width="80">Телефон</td>
        <td id="headb" align="center" width="100">Email</td>                      
        <td id="headb" align="center" width="70">Оплата</td>    
        <td id="headb" align="center" width="70">Доставка</td>    
        <td id="headb" align="center" width="40">Доставка<br/>грн</td>    
        <td id="headb" align="center" width="40">Кол-во товаров</td>
        <td id="headb" align="center" width="40">Итого<br/>грн</td>            
        <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
    </tr>       
<{section name=i loop=$items}>
    <tr<{if $items[i].color_hex}> style="background:#<{$items[i].color_hex}>"<{/if}>>             
        <td align="center"><b><{$items[i].id}></b></td>
        <td align="center"><b><{$items[i].substrate_title}></b></td>
        <td align="center">
            <{if $items[i].manager_id}>
                <a href="/admin.php?module=users&task=editItem&itemID=<{$items[i].manager_id}>" target="_blank"><{$items[i].manager_title}></a>
            <{else}> -- <{/if}>
        </td> 
        <td align="center"><{HTMLHelper::formatDate($items[i].created)}></td> 
        <td align="center"><{if $items[i].planned}><{HTMLHelper::formatDate($items[i].planned, true)}><{else}> -- <{/if}></td> 
        <td align="center"><{if $items[i].closed}><{HTMLHelper::formatDate($items[i].closed)}><{else}> -- <{/if}></td>  
        <td align="center"><strong><{$items[i].status_title}></strong></td> 
        <td align="left"><{$items[i].admin_comment}></td>  
        <td align="center">
            <a href="/admin.php?module=customers&task=editItem&itemID=<{$items[i].user_id}>" target="_blank" title="<{$items[i].user_title}>"><{if $items[i].name}><{$items[i].name}><{else}><{$items[i].user_title}><{/if}></a>
        </td> 
        <td align="center"><{$items[i].phone}></td>
        <td align="center"><{if $items[i].email}><{$items[i].email}><{else}> -- <{/if}></td>                      
        <td align="center"><{if $items[i].payment_title}><{if $items[i].payment_id==OrderHelper::PAYMENT_TYPE_CARD}><font color="red"><b><{$items[i].payment_title}></b></font><{else}><{$items[i].payment_title}><{/if}><{else}> -- <{/if}></td>            
        <td align="center"><{if $items[i].shipping_title}><{$items[i].shipping_title}><{else}> -- <{/if}></td>            
        <td align="center"><{if $items[i].shipping_price > 0}><{HTMLHelper::formatPrice($items[i].shipping_price)}><{else}><{$items[i].shipping_price_title}><{/if}></td>            
        <td align="center"><{$items[i].total_qty}></td>
        <td align="center"><{HTMLHelper::formatPrice($items[i].total_price)}></td>                      
        <td align="center">
            <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
            </a>
        </td>
    </tr>
<{/section}>
</table>
<table width="100%" border="0" cellspacing="10" cellpadding="10">
    <tr>            
        <td align="left">
<{if $arrPageData.total_pages>1}>
            <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1}>
            <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
        </td>
        <td align="right">
            <b>Всего заказов:</b> <{$arrPageData.total_items}><br/>
            <b>Всего товаров:</b> <{$arrPageData.totals.qty}><br/>
            <b>Всего, грн:</b> <{HTMLHelper::formatPrice($arrPageData.totals.price)}>
        </td>
    </tr>
</table>
              
<script type="text/javascript">              
    $(function() {     
        initDatePickers();
        
        $('#categorySearch').autocomplete({
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
                                value: item.value,
                            }
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#created_from').val('');
                $('#created_to').val('');
            },
            minLength: 2
        });
    });
</script>
<{/if}>
</div>