<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.ORDERS creat_title=$smarty.const.ADMIN_CREATING_NEW_ORDER edit_title=$smarty.const.ADMIN_EDIT_ORDER}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block">

<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>

<div class="search_block stylized">
    <form method="GET" id="searchForm" action="">
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        
        <div class="row">
            <div class="inline">создан</div>
            <input type="text" class="datepicker datetimerange" name="" size="37" style="font-size:15px" data-name="created"
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
            
            <button type="submit" class="buttons inline-block" style="margin-left:25px;">Применить</button>
            
            &nbsp;&nbsp; <a href="<{$arrPageData.admin_url}>" style="margin-left:25px;">Сбросить</a>
        </div>
        
        <div class="row">
            <div class="inline">запланирован</div>
            <input type="text" class="datepicker datetimerange" name="" size="37" style="font-size:15px" data-name="planned"
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
    </form>
</div>
<div class="clear"></div> 

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
    <tr>
        <td id="headb" align="center" width="50">№ заказа</td>
        <td id="headb" align="center" width="100">Менеджер</td>            
        <td id="headb" align="center" width="50">Создан</td>
        <td id="headb" align="center" width="50">Заплани-<br/>рован</td>          
        <td id="headb">Коммент менеджера</td>                 
        <td id="headb" align="center" width="120">Доставка</td>    
    </tr>  
<{section name=i loop=$items}>
    <tr<{if $items[i].color_hex}> style="background:#<{$items[i].color_hex}>"<{/if}>>             
        <td align="center"><b><{$items[i].id}></b></td>
        <td align="center">
            <{if $items[i].manager_id}>
                <{$items[i].manager_title}></a>
            <{/if}>
        </td> 
        <td align="center"><{HTMLHelper::formatDate($items[i].created)}></td> 
        <td align="center"><{if $items[i].planned}><{HTMLHelper::formatDate($items[i].planned)}><{else}> -- <{/if}></td> 
        <td><b><{$items[i].admin_comment}></b></td>                
        <td align="center"><{if $items[i].shipping_title}><{$items[i].shipping_title}><{else}> -- <{/if}></td>                  
    </tr>
    <{section name=j loop=$items[i].arProducts}>
        <tr style="background:<{OrderHelper::getProductBG($items[i].arProducts[j])}>">
            <td colspan="6" class="no_padding">
                <table width="100%">
                    <tr>
                        <td width="50"></td>
                        <td width="100" align="center">
                            <a href="<{$items[i].arProducts[j].product_image}>" class="highslide" onclick="return hs.expand(this);">
                                <img src="<{$items[i].arProducts[j].product_image}>" width="50" style="border:none"/>
                            </a>
                        </td>
                        <td width="100" align="left"><b>Код:</b><br/> <a href="/admin.php?module=prints&task=editItem&itemID=<{$items[i].arProducts[j].product_id}>" target="_blank"><{$items[i].arProducts[j].pcode}></a></td>                        
                        <td width="150" align="left"><b>Тип:</b><br/> <{$items[i].arProducts[j].substrate_title}></td>
                        <td width="100" align="left"><b>Положение:</b><br/> <{PrintProduct::$arSides[$items[i].arProducts[j].placement]}></td>
                        <td width="80" align="left"><b>Цвет:</b><br/> <{$items[i].arProducts[j].color_title}></td>
                        <td width="80" align="left"><b>Размер:</b><br/> <{$items[i].arProducts[j].size_title}></td>
                        <td width="80" align="left"><b>Кол-во:</b><br/> <{$items[i].arProducts[j].qty}></td>
                        <td><{if $items[i].arProducts[j].admin_comment}><b>Коммент:</b><br/> <b style="color:red"><{$items[i].arProducts[j].admin_comment}></b><{/if}></td>
                        <td align="right">
                            <div class="actions badges">
                                <{OrderHelper::getIndustryActions($items[i].arProducts[j])}>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <{/section}>
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
        <td align="right"><b>Всего заказов:</b> <{$arrPageData.total_items}></td>
    </tr>
</table>

<script type="text/javascript">    
    function setAction(btn) {
        $.ajax({
            url: '<{$arrPageData.current_url}>'+'&task=setAction&itemID='+$(btn).data('id')+'&column='+$(btn).data('column'),
            type: 'GET',
            dataType: 'json',
            success: function(json) {
                $(btn).closest('tr').attr('style', 'background:'+json.bg);
                $(btn).closest('.actions').html(json.output);                
            }
        });
    }
    
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
                                value: item.title,
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
</div>