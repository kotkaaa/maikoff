<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.SUBSTRATES creat_title=$smarty.const.ADMIN_CREATING_NEW_PRODUCT_TYPE edit_title=$smarty.const.ADMIN_EDIT_PRODUCT_TYPE}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' or $arrPageData.task=='editItem'}>
    <form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
        <div class="tabsContainer">
            <ul class="nav">
                <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
                <li><a href="javascript:void(0);" data-target="attributes">Характеристики</a></li>
<{if $arrPageData.task=='editItem'}>
                <li><a href="javascript:void(0);" data-target="images">Изображения</a></li>
<{/if}>
                <li><a href="javascript:void(0);" data-target="seo">SEO</a></li>
                <li><a href="javascript:void(0);" data-target="history">История</a></li>
            </ul>
            <div class="tab_line"></div>
            <ul class="tabs">
                <li class="active" id="tab_main">
                    <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                        <tr>
                            <td id="headb" align="left" width="120">Название <font style="color:red">*</font></td>
                            <td>
                                <input class="left" name="title" size="75" id="title" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.title}>" />
                                <input type="button" class="buttons left" value="Изменить SEO путь" onclick="MoveToSeoPath();"/>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Название в единственном числе <font style="color:red">*</font></td>
                            <td align="left">
                                <input name="title_s" id="title_s" size="75" type="text" value="<{$item.title_s}>"/>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Название в множественном числе <font style="color:red">*</font></td>
                            <td align="left">
                                <input name="title_p" id="title_p" size="75" type="text" value="<{$item.title_p}>"/>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Короткое название</td>
                            <td align="left">
                                <input name="title_short" id="title_short" size="75" type="text" value="<{$item.title_short}>"/>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td id="headb" align="left"><{$smarty.const.HEAD_PRICE}></td>
                            <td align="left">
                                <input name="price" id="price" size="7" type="text" value="<{$item.price}>"/> грн
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Размеры <font style="color:red">*</font></td>
                            <td align="left">
<{section name=i loop=$arrPageData.arSizes}>
                                <input type="checkbox" name="sizes[]" id="sizes_<{$arrPageData.arSizes[i].id}>" value="<{$arrPageData.arSizes[i].id}>" <{if in_array($arrPageData.arSizes[i].id, $item.sizes)}>checked<{/if}>/>
                                <label for="sizes_<{$arrPageData.arSizes[i].id}>"><{$arrPageData.arSizes[i].title}></label> &emsp;
<{/section}>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td id="headb" align="left">Таблица размеров</td>
                            <td align="left">
                                <select class="field" name="size_grid_id">
                                    <option value="0">Не выбрано</option>
<{section name=i loop=$arrPageData.arGrids}>
                                    <option value="<{$arrPageData.arGrids[i].id}>" <{if $item.size_grid_id==$arrPageData.arGrids[i].id}>selected<{/if}>><{$arrPageData.arGrids[i].title}></option>
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
<{if $arrPageData.task=='editItem'}>
                <li id="tab_images">
                    <br/>
                    <input type="file" name="files[]" id="fileupload" style="display: none;"/>
                    <table cellspacing="0" cellpadding="1" border="0" class="sheet">
                        <tr>
                            <td width="600">
                                <table border="1" cellspacing="0" cellpadding="1" class="list colored images-upload-list" id="imagesTable">
                                    <thead>
                                        <tr>
                                            <td id="headb" align="left"  width="140">цвет</td>
                                            <td id="headb" align="center">перед</td>
                                            <td id="headb" align="center">спина</td>
                                            <td id="headb" align="center" width="40">сорт.</td>
                                        </tr>
                                    </thead>
                                    <tbody>
<{foreach name=i from=$arrPageData.arColors item=color}>
                                        <tr>
                                            <td align="left" style="padding-top: 4px; padding-bottom: 4px; border-bottom: 1px solid #CCCCCC;">
                                                <input type="hidden" name="arImages[]" value="<{$color.id}>"/>
                                                <div style="display: inline-block; vertical-align: middle; width: 30px; height: 30px; border: 1px solid #e0e0e0; background-color: #<{$color.hex}>"></div>
                                                <{$color.title}>
                                                <{*$color.order}>
                                                <{$color.cnt*}>
                                            </td>
                                            <td align="center" style="padding-top: 4px; padding-bottom: 4px; border-bottom: 1px solid #CCCCCC;">
                                                <img src="<{$arrPageData.files_url}><{if array_key_exists($color.id, $item.images) and !empty($item.images[$color.id].img_front)}><{$item.images[$color.id].img_front}><{else}>noimage.jpg<{/if}>" class="preview"/>
                                                <img class="del" src="/images/admin/error.png" alt="" data-placement="front" data-colorid="<{$color.id}>" data-itemid="<{$item.id}>"/>
                                                <button class="browse" data-placement="front" data-colorid="<{$color.id}>" data-itemid="<{$item.id}>" data-filename="<{if array_key_exists($color.id, $item.images)}><{$item.images[$color.id].img_front}><{/if}>">Выбрать</button>
                                            </td>
                                            <td align="center" style="padding-top: 4px; padding-bottom: 4px; border-bottom: 1px solid #CCCCCC;">
                                                <img src="<{$arrPageData.files_url}><{if array_key_exists($color.id, $item.images) and !empty($item.images[$color.id].img_rear)}><{$item.images[$color.id].img_rear}><{else}>noimage.jpg<{/if}>" class="preview"/>
                                                <img class="del" src="/images/admin/error.png" alt="" data-placement="rear" data-colorid="<{$color.id}>" data-itemid="<{$item.id}>"/>
                                                <button class="browse" data-placement="rear" data-colorid="<{$color.id}>" data-itemid="<{$item.id}>" data-filename="<{if array_key_exists($color.id, $item.images)}><{$item.images[$color.id].img_rear}><{/if}>">Выбрать</button>
                                            </td>
                                            <td align="center" style="padding-top: 4px; padding-bottom: 4px; border-bottom: 1px solid #CCCCCC;">
                                                <img src="/images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" style="cursor: pointer;"/>
                                            </td>
                                        </tr>
<{/foreach}>
                                    </tbody>
                                </table>
                            </td>
                            <td>&nbsp;</td>
                            <td valign="top">
                                <table border="1" cellspacing="0" cellpadding="1" class="sheet">
                                    <tr>
                                        <td id="headb" colspan="2" style="padding: 3px;">Перед</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 3px;">Размер</td>
                                        <td style="padding: 3px;">
                                            <input type="text" size="5" name="dimensions[front][width]" value="<{$item.dimensions.front.width}>"> рх
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 3px;">Позиция</td>
                                        <td style="padding: 3px;">
                                            <input type="text" size="5" name="dimensions[front][offset]" value="<{$item.dimensions.front.offset}>"> рх
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 3px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td id="headb" colspan="2" style="padding: 3px;">Спина</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 3px;">Размер</td>
                                        <td style="padding: 3px;">
                                            <input type="text" size="5" name="dimensions[rear][width]" value="<{$item.dimensions.rear.width}>"> рх
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 3px;">Позиция</td>
                                        <td style="padding: 3px;">
                                            <input type="text" size="5" name="dimensions[rear][offset]" value="<{$item.dimensions.rear.offset}>"> рх
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                    </table>
                    <script type="text/javascript">
                        $(function(){
                            var Table = $("#imagesTable");
                            Table.on("click", ".browse", function(e){
                                e.preventDefault();
                                var colorID   = $(this).data("colorid"),
                                    itemID    = $(this).data("itemid"),
                                    placement = $(this).data("placement");
                                $("#fileupload").data({
                                    colorid: colorID,
                                    itemid: itemID,
                                    placement: placement
                                }).trigger("click");
                            }).on("click", ".del", function(e){
                                e.preventDefault();
                                var $btn      = $(this),
                                    colorID   = $btn.data("colorid"),
                                    itemID    = $btn.data("itemid"),
                                    placement = $btn.data("placement");
                                $.ajax({
                                    url: "<{$arrPageData.admin_url}>",
                                    type: "GET",
                                    dataType: "json",
                                    data: {
                                        task: "unLinkFile",
                                        itemID: itemID,
                                        colorID: colorID,
                                        placement: placement
                                    },
                                    beforeSend: function(){
                                        $("body").addClass("ajax-load");
                                    },
                                    complete: function(){
                                        $("body").removeClass("ajax-load");
                                    },
                                    success: function(json){
                                        if (json.filename) $btn.siblings(".preview").attr("src", json.filename);
                                    }
                                });
                            });
                            $('#fileupload').fileupload({
                                url: "/interactive/ajax.php?zone=site&action=ajaxFileUpload",
                                dataType: 'json',
                                autoUpload: true,
                                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                                maxFileSize: 999000
                            }).on("fileuploaddone", function(e, data){
                                var btn,img,
                                    target    = $(e.currentTarget),
                                    colorID   = target.data("colorid"),
                                    itemID    = target.data("itemid"),
                                    placement = target.data("placement");
                                $.map(Table.find(".browse"), function(el){
                                    var $btn = $(el);
                                    if ($btn.data("colorid")==colorID && $btn.data("placement")==placement) {
                                        btn = $btn;
                                        img = $btn.siblings(".preview");
                                    }
                                });
                                $.map(data.result.files, function (file, index) {
                                    if (file.name) {
                                        $.ajax({
                                            url: "<{$arrPageData.admin_url}>",
                                            type: "GET",
                                            dataType: "json",
                                            data: {
                                                task: "fileUpload",
                                                itemID: itemID,
                                                colorID: colorID,
                                                placement: placement,
                                                filename: file.name
                                            },
                                            success: function(json){
                                                if (json.filename) img.attr("src", json.filename+'?t='+$.now());
                                            },
                                            beforeSend: function(){
                                                $("body").addClass("ajax-load");
                                            },
                                            complete: function(){
                                                $("body").removeClass("ajax-load");
                                            }
                                        }); return;
                                    }
                                });
                            });
                        });
                    </script>
                </li>
<{/if}>
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
        function formCheck(form) {
            if (form.title.value.length == 0){
               alert('Не указано название!'); 
               return false;
            }
            if (form.title_s.value.length == 0){
               alert('Не указано название в единичном числе!'); 
               return false;
            }
            if (form.title_p.value.length == 0){
               alert('Не указано название в множественном числе!'); 
               return false;
            }
            if (form.price.value.length == 0){
               alert('Не указана цена!'); 
               return false;
            } return true;
        }
    </script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    <div class="clear"></div>
    <{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_PRODUCT_TYPE shortcut=false}>
    <form method="POST" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored" id="operationTbl">
            <tr>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="center" width="38">№</td>
                <td id="headb" align="center" width="80"></td>
                <td id="headb" align="left">Название</td>
                <td id="headb" align="center" width="62"><{$smarty.const.HEAD_PRICE}></td>
                <td id="headb" align="center" width="150">Размеры</td>
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
                <td align="center">
                    <{$items[i].id}>
                </td>
                <td align="center">
                    <a href="<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>">
                        <img src="<{$items[i].image}>" alt="" height="72"/>
                    </a>
                </td>
                <td>
                    <a href="<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a>
                </td>
                <td align="center">
                    <input type="text" size="7" value="<{$items[i].price}>" name="arPrices[<{$items[i].id}>]"/>
                </td>
                <td align="center"><{$items[i].sizes}></td>
                <td align="center">
                    <a href="<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                        <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                    </a>
                </td>
                <td align="center">
<{if $items[i].edit}>
                    <a href="<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
                       <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
                    </a>
<{else}>
                    --
<{/if}>
                </td>
            </tr>
<{/section}>
        </table>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr>
                <td align="right">
                    <input name="submit_order" class="buttons" type="submit" value="<{$smarty.const.BUTTON_APPLY}>" />
                </td>
            </tr>
        </table>
    </form>
<{/if}>
</div>