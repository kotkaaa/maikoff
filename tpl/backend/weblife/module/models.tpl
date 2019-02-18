<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title='Каталог моделей' creat_title='Создание модели' edit_title='Редактирование модели'}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<{if !empty($item)}>
    <a href="/admin.php?module=catalog&modelID=<{$item.id}>" class="inline-block right" style="margin-top:-22px;margin-right:10px;">К списку товаров модели &#8594; </a>
    <div class='clear'></div>
<{else}>
    <{include file='common/left_menu.tpl' dependID=0 categoryTree=$arrPageData.arCategoryTree islist=true}>
<{/if}>
<div id="right_block" class="catalog">    
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);">
    <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
    <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
    <input type="hidden" name="order"   value="<{$item.order}>"   />
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
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
                            <br/>Название <font style="color:red">*</font> <a href="#" title="{title}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td>
                            <br/>
                            <input class="left" name="title" size="55" id="title" style="margin-right:20px;" type="text" value="<{$item.title}>" /> 
                            <b>Код модели</b> <font style="color:red">*</font>&nbsp;&nbsp;&nbsp;
                            <input name="pcode" id="pcode" size="20" type="text" value="<{$item.pcode}>" />
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0 disableCopy=1}>
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
                        <td id="headb" align="left">Категория <font style="color:red">*</font></td>
                        <td align="left">
                            <{if !empty($arrPageData.arCategoryTree)}>
                                <select class="field" name="category_id"<{if !empty($item.category_id) OR !empty($arrPageData.category_id)}> onchange="hideApplyBut(this, this.form.submit_apply, <{if !empty($item.category_id)}><{$item.category_id}><{else}><{$arrPageData.category_id}><{/if}>);"<{/if}>>
                                    <option value="0">Выберите категорию</option>
<{section name=i loop=$arrPageData.arCategoryTree}>
                                    <option value="<{$arrPageData.arCategoryTree[i].id}>"<{if $item.category_id==$arrPageData.arCategoryTree[i].id OR (empty($item.category_id) && $arrPageData.category_id==$arrPageData.arCategoryTree[i].id)}> selected<{/if}>>
                                        <{$arrPageData.arCategoryTree[i].margin}> <{$arrPageData.arCategoryTree[i].title}> 
                                        &nbsp; [<{$smarty.const.HEAD_ITEMS}>: <{if isset($arrPageData.arCategoryTree[i].itemsCnt)}><{$arrPageData.arCategoryTree[i].itemsCnt}><{else}>0<{/if}>] &nbsp; <{if $arrPageData.arCategoryTree[i].active==0}>( <{$smarty.const.HEAD_INACTIVE}> ) &nbsp; <{/if}>
                                    </option>
<{if !empty($arrPageData.arCategoryTree[i].childrens)}>
                                    <!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
                                    <{include file='common/tree_childrens.tpl' dependID=$item.category_id arrChildrens=$arrPageData.arCategoryTree[i].childrens}>
                                    <!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                                </select>
                            <{else}>                       
                                <input type="hidden" name="category_id" value="<{$item.category_id}>"/>
                            <{/if}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">
                            Бренд <a href="#" title="{brand}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td align="left">
                            <select id="brand_id" name="brand_id" style="min-width: 150px" <{if $arrPageData.task=='editItem'}>disabled<{/if}> 
                                    onchange="$('#series_id').val(0);$('#series_id').find('option').addClass('hidden_block');$('#series_id').find('[data-brand=\''+$(this).val()+'\']').removeClass('hidden_block');">
                                <option value="0"> -- <{$smarty.const.LABEL_SELECT}> -- </option>
<{section name=i loop=$arrPageData.arBrands}>
                                <option value="<{$arrPageData.arBrands[i].id}>" <{if $item.brand_id==$arrPageData.arBrands[i].id}>selected<{/if}>>
                                    <{$arrPageData.arBrands[i].title}>
                                </option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">
                            Серия <a href="#" title="{series}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td align="left">
                            <select id="series_id" name="series_id" style="min-width: 150px" <{if $arrPageData.task=='editItem'}>disabled<{/if}>>
                                <option value="0"> -- <{$smarty.const.LABEL_SELECT}> -- </option>
<{section name=i loop=$arrPageData.arSeries}>
                                <option <{if $arrPageData.task=='addItem'}>class="hidden_block"<{/if}> data-brand="<{$arrPageData.arSeries[i].brand_id}>" 
                                        value="<{$arrPageData.arSeries[i].id}>" <{if $item.series_id==$arrPageData.arSeries[i].id}>selected<{/if}>>
                                    <{$arrPageData.arSeries[i].title}>
                                </option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">
                            Размеры <a href="#" title="{sizes}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td align="left">
<{section name=i loop=$arrPageData.arSizes}>
    <{if in_array($arrPageData.arSizes[i].id, $item.arSizes)}> 
        <{assign var=checked value=1}>
    <{else}>
        <{assign var=checked value=0}>
    <{/if}>
                            <label style="padding-right:10px;font-size:15px;<{if $checked}>font-weight:bold<{/if}>" class="size">
                                <input type="checkbox" name="arSizes[]" value="<{$arrPageData.arSizes[i].id}>"<{if $checked}> checked<{/if}> 
                                       onchange="$(this).closest('label').css('font-weight',(this.checked ? 'bold' : ''));"/> <{$arrPageData.arSizes[i].title}>
                            </label>
<{/section}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">Таблица размеров</td>
                        <td align="left">
                            <select name="size_grid_id" style="min-width: 150px">
                                <option value="0"> -- <{$smarty.const.LABEL_SELECT}> -- </option>
<{section name=i loop=$arrPageData.arSizeGrids}>
                                <option value="<{$arrPageData.arSizeGrids[i].id}>" <{if $item.size_grid_id==$arrPageData.arSizeGrids[i].id}>selected<{/if}>>
                                    <{$arrPageData.arSizeGrids[i].title}>
                                </option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left" valign="top">
                            Цвета <font style="color:red">*</font> <a href="#" title="{color}" class="jtooltip"><img src="/images/admin/tooltip.png" align="top" width="16"/></a>
                        </td>
                        <td align="left">
<{section name=i loop=$arrPageData.arColors}>
    <{if in_array($arrPageData.arColors[i].id, $item.arColors)}> 
        <{assign var=checked value=1}>
    <{else}>
        <{assign var=checked value=0}>
    <{/if}>
                            <div class="inline-block colors-row<{if $arrPageData.arColors[i].disabled}> disabled<{/if}>" style="min-width:150px">
                                <label <{if $checked}>style="font-weight:bold"<{/if}>>
                                    <div class="color" style="background:#<{$arrPageData.arColors[i].hex}>;"></div>
                                    <div><input type="checkbox" name="arColors[]" value="<{$arrPageData.arColors[i].id}>"<{if $checked}> checked<{/if}> 
                                                <{if $arrPageData.arColors[i].disabled}> onclick="return false;"<{/if}>
                                                onchange="$(this).closest('label').css('font-weight',(this.checked ? 'bold' : ''));"/> <{$arrPageData.arColors[i].title}> </div>
                                </label>
                                <center><br/>
                                    <input type="text" size="10" name="arColorCodes[<{$arrPageData.arColors[i].id}>]" value="<{if array_key_exists($arrPageData.arColors[i].id, $item.arColorCodes)}><{$item.arColorCodes[$arrPageData.arColors[i].id]}><{/if}>"/>
                                </center>
                            </div>
<{/section}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{if $arrPageData.task == 'addItem'}>
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
                            Виды печати 
                        </td>
                        <td align="left">
<{section name=i loop=$arrPageData.arPrintTypes}>
                            <label>
                                <input type="checkbox" name="arPrintTypes[]" value="<{$arrPageData.arPrintTypes[i].id}>"/> <{$arrPageData.arPrintTypes[i].title}>
                            </label>
<{/section}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{/if}>
                    <tr>
                        <td id="headb" align="left">Стикеры</td>
                        <td align="left">
                            <label>
                                <input type="hidden" name="is_fast_print" value="0"/>
                                <input type="checkbox" name="is_fast_print" value="1"<{if $item.is_fast_print}> checked<{/if}>/> 
                                Быстрая печать
                            </label>
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
                    
                    <tr>
                        <td id="headb" align="left">Связанная статья</td>
                        <td align="left">
                            <select name="article_id" style="min-width: 150px">
                                <option value="0"> -- <{$smarty.const.LABEL_SELECT}> -- </option>
<{section name=i loop=$arrPageData.arArticles}>
                                <option value="<{$arrPageData.arArticles[i].id}>" <{if $item.article_id==$arrPageData.arArticles[i].id}>selected<{/if}>>
                                    <{$arrPageData.arArticles[i].title}>
                                </option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
            </li>
                        
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
                                    - бренд <b>{brand}</b><br/>
                                    - серия <b>{series}</b><br/>
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
                </table>
            </li>
            
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>

<script type="text/javascript">
    $(function() {
        $('.jtooltip').tooltip({
            position: {
                my: "left bottom", // the "anchor point" in the tooltip element
                at: "left top-5", // the position of that anchor point relative to selected element
            }
        });
        <{if $item.brand_id}>
            $('#series_id').find('[data-brand=\'<{$item.brand_id}>\']').removeClass('hidden_block');   
        <{/if}>
    });
    function formCheck(form) {
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
        if(form.pcode.value == 0){
           alert('Введите код модели'); 
           return false;
        }
        if($('.colors-row').find('input:checked') == 0){
           alert('Выберите цвет товара'); 
           return false;
        }
<{if $arrPageData.task == 'addItem'}>
        if(form.price.value == 0){
           alert('Укажите цену'); 
           return false;
        }
<{/if}>
        return true;
    }    
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}> 
<{include file='common/new_page_btn.tpl' title='модель'}>
<div class="search_block" style="padding:0 0 10px">
    <form method="GET" id="searchForm" action="">
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        <input size="44" type="text" placeholder="поиск по коду/названию модели" id="categorySearch" name="filters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" />
        <div class="inline-block">
            <div>Бренд</div>
            <select name="filters[brand_id]" style="width:100px">
                <option value="0">не выбрано</option>
                <{section name=i loop=$arrPageData.arBrands}>
                    <option value="<{$arrPageData.arBrands[i].id}>"<{if isset($arrPageData.filters.brand_id) && $arrPageData.filters.brand_id==$arrPageData.arBrands[i].id}> selected<{/if}>>
                        <{$arrPageData.arBrands[i].title}>
                    </option>
                <{/section}>
            </select>
        </div>
        <div class="inline-block" >
            <div>Серия</div>
            <select name="filters[series_id]" style="width:100px">
                <option value="0">не выбрано</option>
                <{section name=i loop=$arrPageData.arSeries}>
                    <option value="<{$arrPageData.arSeries[i].id}>"<{if isset($arrPageData.filters.series_id) && $arrPageData.filters.series_id==$arrPageData.arSeries[i].id}> selected<{/if}>>
                        <{$arrPageData.arSeries[i].title}>
                    </option>
                <{/section}>
            </select>      
        </div>
        <label><input type="checkbox" name="filters[show_all]" value="1"<{if isset($arrPageData.filters.show_all)}> checked<{/if}>/> показать все</label>        
        <a href="<{$arrPageData.admin_url}>" class="buttons right" style="margin-top:16px;margin-right:3px;height:24px;line-height:24px;color:#fff">Сбросить</a>
        <button type="submit" class="buttons right" style="margin-top:16px;margin-right:3px;">Фильтр</button>
    </form>
</div>
<div class="clear"></div>

<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored" id="operationTbl">
        <tr>
            <{if $arrPageData.total_items>1}>
            <td id="headb" align="center" width="12"></td>
            <{/if}>
            <td id="headb" align="center" width="38"></td>
            <td id="headb" align="center" width="50">Код</td>
            <td id="headb" align="center" width="50"></td>
            <td id="headb" align="left">Название</td>
            <td id="headb" align="left" width="150">Бренд</td>
            <td id="headb" align="left" width="150">Серия</td>
            <td id="headb" align="center" width="38">Сорт.</td>
            <td id="headb" align="center" width="38">Товары</td>
            <td id="headb" align="center" width="38">Ред.</td>            
            <td id="headb" align="center" width="38">Удал.</td>
        </tr>
<{section name=i loop=$items}>
        <tr>
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
            <td align="center"><{$items[i].pcode}></td>
            <td align="center"><{if $items[i].default_image}><img src="<{$items[i].default_image}>" height="30"/><{/if}></td>
            <td align="left"><{$items[i].title}></td>
            <td align="left"><{$items[i].brand_title}></td>
            <td align="left"><{$items[i].series_title}></td>
            <td align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="order" value="<{$items[i].order}>" maxlength="4"/>
            </td>
            <td align="center">
                <a href="/admin.php?module=catalog&modelID=<{$items[i].id}>">
                    <img src="/images/operation/add_tree.png"/> <{$items[i].products_cnt}>
                </a>
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
                        <li data-val="delete" onclick="$(this).parent().parent().find('input').val($(this).data('val')); $(this).closest('form').submit();">
                            <img src="/images/operation/delete.png"/>&nbsp;&nbsp;удалить
                        </li>
                    </ul>
                    <input type="hidden" name="allitems" value=""/>
                </div>
            </td>
<{else}>
            <td colspan="2"></td>
<{/if}>
            <td align="center" width="350">
                <{if $arrPageData.total_pages>1}>
                    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <{/if}>
            </td>
            <td align="right">
                <input name="submit_order" class="buttons" type="submit" value="Применить сортировку" style="margin-right:100px" />
            </td>
        </tr>
    </table>
</form>
                
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
</div>
