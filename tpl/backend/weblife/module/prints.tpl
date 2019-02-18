<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title='Принты' creat_title='Создание принта' edit_title='Редактирование принта'}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<div id="right_block">
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
    <input type="hidden" name="createdDate" value="<{$item.createdDate}>"/>
    <input type="hidden" name="createdTime" value="<{$item.createdTime}>"/>
    <input type="hidden" name="order"   value="<{$item.order}>"/>

    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <{if $arrPageData.task == 'editItem'}>
                <li><a href="javascript:void(0);" data-target="logos">Логотипы</a></li>
            <{/if}>
            <li><a href="javascript:void(0);" data-target="attributes">Характеристики</a></li>
            <li><a href="javascript:void(0);" data-target="seo">SEO</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                    <tr>
                        <td id="headb" align="left" width="120">
                            <{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font>
                            <a href="#" title="{title}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td>
                            <input class="left" name="title" size="77" id="title" type="text" value="<{$item.title}>" style="margin-top:5px;margin-right:10px;"/> 
                            <input type="button" class="buttons left" value="Изменить SEO путь" onclick="MoveToSeoPath();"/>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0 disableCopy=1}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">Артикул <font style="color:red">*</font> </td>
                        <td align="left">
                            <input  name="pcode" id="pcode" size="15" type="text" value="<{$item.pcode}>" />
                        </td>
                        <td class="buttons_row"></td>
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
<{if !empty($arrPageData.categoryTree)}>
                    <tr>
                        <td id="headb" align="left">
                            <{$smarty.const.HEAD_CATEGORY}> <font style="color:red">*</font>
                            <a href="#" title="{category}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td align="left">
                            <select  name="category_id"<{if !empty($item.category_id) OR !empty($arrPageData.category_id)}> onchange="hideApplyBut(this, this.form.submit_apply, <{if !empty($item.category_id)}><{$item.category_id}><{else}><{$arrPageData.category_id}><{/if}>);"<{/if}>>
<{section name=i loop=$arrPageData.categoryTree}>
                                <option value="<{$arrPageData.categoryTree[i].id}>"<{if $item.category_id==$arrPageData.categoryTree[i].id OR (empty($item.category_id) && $arrPageData.category_id==$arrPageData.categoryTree[i].id)}>  selected<{/if}>>
                                    <{$arrPageData.categoryTree[i].margin}><{$arrPageData.categoryTree[i].title}> 
                                    &nbsp; [<{$smarty.const.HEAD_ITEMS}>: <{if isset($arrPageData.arCidCntItems[$arrPageData.categoryTree[i].id])}><{$arrPageData.arCidCntItems[$arrPageData.categoryTree[i].id]}><{else}>0<{/if}>] 
                                    &nbsp; <{if $arrPageData.categoryTree[i].active==0}>(<{$smarty.const.HEAD_INACTIVE}>)<{/if}>
                                </option>
<{if !empty($arrPageData.categoryTree[i].childrens)}>
                                <!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/tree_childrens.tpl' dependID=$item.category_id arrChildrens=$arrPageData.categoryTree[i].childrens}>
                                <!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{else}>
                    <input type="hidden" name="category_id" value="<{$item.category_id}>"/>
<{/if}>
                    <tr>
                        <td id="headb" align="left">Доп. категории</td>
                        <td align="left">
                            <div>
                                <select id="extraCats" name="categories[]" class="searchAttrValue" style="width: 500px;" multiple>
<{section name=l loop=$arrPageData.arCategories}>
                                    <option value="<{$arrPageData.arCategories[l].id}>" <{if !empty($item.categories) && in_array($arrPageData.arCategories[l].id, $item.categories)}>selected<{/if}>>
                                        <{$arrPageData.arCategories[l].title}>
                                    </option>
<{/section}>
                                </select>
                                <div class="error-handler hidden_block"></div>
                            </div>
                        </td>
                        <td class="buttons_row"></td>
                    </tr> 
                    
                    <tr>
                        <td id="headb" align="left">Сторона нанесения<br/>принта <font style="color:red">*</font></td>
                        <td align="left">
                            <div>
                                <{if $item.editableSide}>
                                    <select class="side" name="placement" style="width:180px;">
                                        <{foreach from=$arrPageData.arSides item=side}>
                                            <option value="<{$side.id}>"<{if $item.placement==$side.id}> selected<{/if}>><{$side.title}></option>
                                        <{/foreach}>
                                    </select>
                                <{else}>                                   
                                    <input type="hidden" name="placement" value="<{$item.placement}>"/>
                                    <{$item.placement_title}>
                                <{/if}>
                                <div class="error-handler hidden_block"></div>
                            </div>
                        </td>
                        <td class="buttons_row"></td>
                    </tr> 
                    
                    <tr>
                        <td id="headb" align="left" valign="top">Настройки<br/>типов и цен</td>
                        <td align="left">
                            <div class="assortments" style="position:relative">
                                <div class="loader auto hidden_block"><img src="/images/loader.gif"/></div>
                                <table class="sheet assortment<{if $arrPageData.task=='addItem'}> changeable<{/if}>">
                                    <tr>
                                        <td></td>
                                        <td>
                                            <label>
                                                <input type="checkbox" class="checkboxes check_all" onchange="updateAssortment(this);"/> 
                                                <b>Активные типы</b>
                                            </label>
                                        </td>
                                        <td><b>URL</b></td>
                                        <td><b>Цена, грн</b></td>
                                    </tr>
                                <{foreach from=$item.arAssort key=substrateID item=assort}>
                                    <tr class="row" data-id="<{$assort.id}>" data-substrate="<{$substrateID}>">
                                        <td>     
                                            <input type="hidden" name="arAssort[<{$substrateID}>][id]" value="<{$assort.id}>"<{if $assort.active==0}> disabled<{/if}>/>                                            
                                            <input type="hidden" name="arAssort[<{$substrateID}>][order]" value="<{$assort.order}>"<{if $assort.active==0}> disabled<{/if}>/>                                            
                                            <input type="hidden" name="arAssort[<{$substrateID}>][active]" value="<{$assort.active}>" class="is_active"<{if $assort.active==0}> disabled<{/if}>/>                                            
                                            <{if $assort.substrate_active>0}>
                                            <label>
                                                <input type="radio" name="substrate_id" value="<{$substrateID}>"<{if $arrPageData.task=='editItem'}> onchange="saveAssortment($(this).closest('.row'));"<{/if}><{if $assort.active>0 && $item.substrate_id==$substrateID}> checked<{/if}><{if $assort.active==0}> disabled<{/if}>/>
                                            </label>
                                            <{/if}>
                                        </td>
                                        <td>
                                            <label>
                                                <input type="checkbox" class="checkboxes" onchange="updateAssortment(this);"<{if $assort.active>0}> checked<{/if}>/>                                            
                                                <{$arrPageData.arSubstrates[$substrateID].title}>
                                            </label>
                                        </td>
                                        <td>
                                            <span class="seopath"><{if $assort.id}><{$assort.seo_path}><{else}>не сохранено<{/if}></span>
                                        </td>
                                        <td>
                                            <input type="text" name="arAssort[<{$substrateID}>][price]" value="<{$assort.price}>"<{if $assort.active==0}> disabled<{/if}>/>
                                        </td>
                                    </tr>
                                <{/foreach}>
                                </table>
                            </div>
                        </td>
                        <td class="buttons_row"></td>
                    </tr> 
                    
                    <tr>
                        <td id="headb" align="left" valign="top" style="padding-top:10px !important;">Описание товара</td>
                        <td align="left">
                            <a href="javascript:toggleEditor('fulldescription');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                            <textarea style="width:640px; height: 500px;" id="fulldescription" name="text" ><{$item.text}></textarea>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>   
                </table>
            </li>

            <{if $arrPageData.task == 'editItem'}>
                <li id="tab_logos" style="padding:20px;">       
                    <div id="logos" style="display: inline-block;vertical-align:top;">
                       <{include file="ajax/logos.tpl"}>
                    </div>                
                    <div class="logo_block">
                        <a class="add" href="/admin.php?module=print_assortments&printID=<{$item.id}>&task=addItem&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'Добавление ассортимента <{$item.title}>', objectType:'iframe', preserveContent: false, width:800, marginTop:0});">Добавить логотип</a>
                    </div>                
                </li>
            <{/if}>
            
            <li id="tab_attributes">                
                <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                    <tr>
                        <td id="headb" align="left" width="120" colspan="2">
                            <{include file="common/product-attributes.tpl"}>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0 disableCopy=1}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                </table>
            </li>
            
            <li id="tab_seo">
                <table width="101%" border="0" cellspacing="0" cellpadding="0" class="sheet" >  
                    <tr>
                        <td colspan="2">
                            <strong><{$smarty.const.HEAD_META_DATA}></strong><br/><br/>
                            
                            <div class="inline">META title</div>
                            <input type="text" name="seo_title" id="seo_title" size="112" value="<{$item.seo_title}>" /><br/><br/>
                            
                            <div class="inline">META description</div>
                            <input type="text" name="meta_descr" id="meta_descr" size="112" value="<{$item.meta_descr}>" /><br/><br/>
                            
                            <div class="inline">META key</div>
                            <input type="text" name="meta_key" id="meta_key" size="112" value="<{$item.meta_key}>" /><br/><br/>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0 disableCopy=1}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">
                            <div class="inline" style="vertical-align: top"><b>SEO text</b></div>
                            <div class="inline-block" style="max-width: 590px">
                                <div style="font-style:italic">
                                    В шаблон метаданных вставляются ключи свойств товара: <br/>
                                    - название товара <b>{title}</b><br/>
                                    - подложка, строчными <b>{substrate}, {substrate_l}</b><br/>
                                    - подложка в единичном числе, строчными <b>{substrate_s}, {substrate_sl}</b><br/>
                                    - подложка в множественном числе, строчными<b>{substrate_p}, {substrate_pl}</b><br/>
                                    - цвет <b>{color}</b><br/>
                                    - размеры (через запятую) <b>{sizes}</b><br/>
                                    - категория <b>{category}</b><br/>
                                    - характеристики <b>{attribute_%}</b>, где % - ID характеристики - <b>{attribute_10}</b><br/>
                                    На пользовательской части на место ключей подставляются соответствующие значения свойств. <br/>
                                    В случае с характеристиками, если у модели есть несколько значений одной характеристики - выводится только первое значение.   
                                </div><br/>
                                <a href="javascript:toggleEditor('seoText');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                                <textarea name="seo_text" id="seoText" style="height:500px;"><{$item.seo_text}></textarea>
                            </div>
                        </td>
                        <td class="buttons_row">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">
                            <div class="inline"><{$smarty.const.HEAD_SEO_PATH}></div>
                            <input type="text" size="80" name="seo_path" id="seo_path" value="<{$item.seo_path}>"/>    
                            <input type="button" value="<{$smarty.const.HEAD_GENERATE}>" class="buttons" style="float:right;margin-right:170px;margin-top:0px;" onclick="if(this.form.title.value.length==0){ alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); this.form.title.focus(); return false; } else{ generateSeoPath(this.form.seo_path, this.form.title.value, '<{$arrPageData.module}>'); }" />
                        </td>
                        <td class="buttons_row">&nbsp;</td>
                    </tr>
                </table>
            </li>
            
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>
</div>
<script type="text/javascript">
    function updateAssortment(cb) { 
        var container = $(cb).closest('.assortments');
        var active = $(cb).prop('checked');
        var rows = [];
        if($(cb).hasClass('check_all')) {
            $.each($(cb).closest('.assortments').find('.row'), function(i, row) {
                if($(row).find('.checkboxes').prop('checked') !== active) {
                    rows.push(row);
                }
            });
        } else {
            rows.push($(cb).closest('.row'));           
        }
        SelectCheckBox(cb, '.assortment');
        $.each(rows, function(i, row) {
            var defsubstrate_input = $(row).find('[name="substrate_id"]');
            if(!active && $(defsubstrate_input).prop('checked')) {
                alert('Нельзя отключить дефолтный тип!');
                $(row).find('.checkboxes').prop('checked', true);
            } else {
                var inputs = $(row).find('input').not('.checkboxes');                
                $(row).find('.is_active').val(active ? 1 : 0);
                $(inputs).prop('disabled', !active);
                <{if $arrPageData.task=='editItem'}>
                    saveAssortment(row, inputs);
                <{else}>
                    if(!$(container).find('[name="substrate_id"]:checked').length) {
                        $(defsubstrate_input).prop('checked', true);
                    }
                <{/if}>
            }
        });
    }         
    function saveAssortment(row, inputs) {
        if(typeof inputs == "undefined") inputs = $(row).find('input').not('.checkboxes');
        $.ajax({
            url: '<{$arrPageData.admin_url}>&task=updateAssortment&itemID=<{$item.id}>&substrateID='+$(row).data('substrate')+'&assortID='+$(row).data('id'),
            data: $(inputs).serialize(),
            dataType: 'json',
            type: 'POST',
            beforeSend: function() {
                $('.loader').removeClass('hidden_block');
            },
            success: function (json) {                      
                if(json.error) {
                    alert(json.error);
                    $(inputs).prop('disabled', !active);
                } else if (json.seo_path) {
                    $(row).find('.seopath').text(json.seo_path);
                }
                $('.loader').addClass('hidden_block');
            },
        });
    }
    <{if $arrPageData.task == 'editItem'}>
        hs.Expander.prototype.onAfterClose = function() {
            $.ajax({
                url: '<{$arrPageData.admin_url}>&task=getLogos&itemID=<{$item.id}>',
                dataType: 'json',
                type: 'GET',
                success: function (json) {
                    $('#logos').html(json.output);
                },
            });
        };
    <{/if}>
    $(function(){
        $('#extraCats').select2();
        $('.jtooltip').tooltip({
            position: {
                my: "left bottom", // the "anchor point" in the tooltip element
                at: "left top-5", // the position of that anchor point relative to selected element
            }
        });
    });
    function formCheck(form) {
        if (form.title.value.length == 0) {
            alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>');
            return false;
        }
        if (form.pcode.value.length == 0) {
            alert('Введите код товара!!!');
            return false;
        }
        return true;
    }
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}> 
<{include file='common/new_page_btn.tpl' title='новый принт'}>
<div class="search_block" style="margin-top:-20px;">
    <form method="GET" id="searchForm" action="">
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        <a href="<{$arrPageData.admin_url}>" class="buttons right" style="margin-top:0;margin-left:3px;height:24px;line-height:24px;color:#fff">Сбросить</a>
        <button type="submit" class="buttons right" style="margin-top:0; margin-left:15px;"><{$smarty.const.SITE_FOUND}></button>
        <input size="48" type="text" class="right" placeholder="поиск по артикулу или названию товара" id="categorySearch" name="filters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" />
    </form>
</div>
<div class="clear"></div>
<{include file='common/left_menu.tpl' dependID=0 categoryTree=$arrPageData.categoryTree islist=true}>
<div id='right_block'>
<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored" id="operationTbl">
        <thead>
            <tr>
                <{if $arrPageData.total_items>1}>
                <td id="headb" align="center" width="12"></td>
                <{/if}>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                <td id="headb" align="left" width='200'>Категория</td>
                <td id="headb" align="center" width="62">сорт</td>
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
                <td align="center"><{if $items[i].default_image}><img src="<{$items[i].default_image}>" height="30"/><{/if}></td>
                <td align="left"><{$items[i].title}></td>
                <td align="left"><{$items[i].category}></td>
                <td align="center">
                    <input type="text" name="arItems[<{$items[i].id}>]" id="arSort_<{$items[i].id}>" class="sort" value="<{$items[i].order}>" size="5"/>
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
                <td width="150" align="left" style="padding:6px">                    
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
                <input name="submit_order" class="buttons" type="submit" value="Применить сортировку" style="padding:0 10px;margin-right:45px"/>
            </td>
        </tr>
    </table>
            
    <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td align="center">
<{if $arrPageData.total_pages>1}>
                <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
            </td>
        </tr>
    </table>
</form>
</div>

<script type="text/javascript"> 
    $(function() {    
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
