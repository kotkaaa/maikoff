<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>
<form method="post" id="orderProductsForm" action="<{$arrPageData.current_url|cat:"&orderID="|cat:$arrPageData.orderID|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return OrderProduct.checkForm();">
    <table width="100%" border="1" cellspacing="1" cellpadding="1" class="sheet" style="text-align: left;">
        <tr>
            <td colspan="2" style="padding:10px;">
                <h2><{if $arrPageData.task=='addItem'}>Добавить товар<{else}>Обновить товар <{$item.title}><{/if}></h2>
            </td>
        </tr>
        <tr>
            <td valign="top" width="450">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td id="headb" width="120">Выбрать товар</td>
                            <td>                                
                                <input class="required" type="hidden" name="print_id" value="<{$item.print_id}>"/>
                                <input class="required" type="hidden" name="title" value="<{$item.title}>"/>
                                <input class="required" type="hidden" name="placement" value="<{$item.placement}>"/>
                                <div class="productInfo">
<{if $item.print_id}>
                                    <div class="inline-block" style="width:220px;vertical-align:middle" >
                                        <a href="/admin.php?module=prints&task=editItem&itemID=<{$item.print_id}>" target="_blank">
                                            <{$item.title}>
                                        </a>
                                    </div>
<{/if}>
                                </div>
                                <div class="productSearch<{if $item.print_id}> hidden_block<{/if}>" style="padding-bottom:5px;">
                                    <div style="padding-bottom:10px">Найдите товар по названию/артикулу</div>
                                    <input type="text" id="itemSearch" size="40" value="" placeholder="подбор по артикулу/названию"/>
                                </div>
                            </td>
                        </tr>
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Тип<font color="red">*</font></td>
                            <td>
                                <select autocomplete="off" name="substrate_id" class="required" style="width:250px;" onchange="OrderProduct.getSubstrate($(this).val());">
                                    <option class="default" value="">-- выберите тип --</option>
<{if !empty($item.substrates)}>
<{foreach from=$item.substrates item=type}>
                                    <option value="<{$type.substrate_id}>" data-price="<{$type.price}>"<{if $type.substrate_id==$item.substrate_id}> selected<{/if}>>
                                        <{$type.substrate_title}> (<{$type.price}> грн)
                                    </option>
<{/foreach}>
<{/if}>
                                </select>
                            </td>
                        </tr>
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Цвет<font color="red">*</font></td>
                            <td>
                                <select autocomplete="off" name="color_id" class="required" style="width:250px;" onchange="OrderProduct.setPreview($(this).find('option:selected').data('image'));">
                                    <option class="default" value="">-- выберите цвет --</option>
<{if !empty($item.assortment)}>
<{section name=i loop=$item.assortment}>
                                    <option value="<{$item.assortment[i].color_id}>" data-image="<{$item.assortment[i].middle_image}>"<{if $item.assortment[i].color_id==$item.color_id}> selected<{/if}>>
                                        <{$item.assortment[i].color_title}>
                                    </option>
<{/section}>
<{/if}>
                                </select>
                            </td>
                        </tr>
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Размер<font color="red">*</font></td>
                            <td>
                                <select autocomplete="off" name="size_id" class="required" style="width:250px;">
                                    <option class="default" value="">-- выберите размер --</option>
<{if !empty($item.sizes)}>
<{section name=i loop=$item.sizes}>
                                    <option value="<{$item.sizes[i].id}>" data-cost="<{$item.sizes[i].cost}>" <{if $item.sizes[i].id==$item.size_id}> selected<{/if}>>
                                        <{$item.sizes[i].title}>
                                    </option>
<{/section}>
<{/if}>
                                </select>
                                &nbsp;
                                <span id="sizeCost"><{$item.size_cost|round}></span> грн
                            </td>
                        </tr>
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Кол-во<font color="red">*</font></td>
                            <td>
                                <input autocomplete="off" type="text" class="required" size="10" name="qty" value="<{$item.qty}>"/>
                            </td>
                        </tr>     
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Цена, грн<font color="red">*</font></td>
                            <td>
                                <input autocomplete="off" type="text" class="required" size="10" name="price" value="<{$item.price}>"/>
                            </td>
                        </tr>                 
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Скидка, грн</td>
                            <td>
                                <input autocomplete="off" type="text" size="10" name="discount_value" value="<{$item.discount_value}>"/>
                            </td>
                        </tr>     
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb">Итого, грн</td>
                            <td>
                                <strong class="total_price"><{HTMLHelper::formatPrice($item.total_price)}></strong>
                            </td>
                        </tr>     
                        <tr class="productData<{if !$item.print_id}> hidden_block<{/if}>">
                            <td id="headb" valign="top">Комментарий админа</td>
                            <td>
                                <textarea name="admin_comment" style="width:250px;height:80px"><{$item.admin_comment}></textarea>
                            </td>
                        </tr>      
                    </tbody>
                </table>
            </td>
            <td valign="top" align="center" style="padding:15px">
                <div class="productPreview"><img src="<{$item.product_image}>"/></div>
            </td>
        </tr>
        <tr>
            <td id="headb" colspan="2" align="center">
                <div class="inline-block" style="vertical-align:top">
                    <input type="button" class="buttons" value="Закрыть" onclick="return parent.window.hs.close();"/> &nbsp; 
                    <span style="font-weight:300;font-size:11px;text-align:left">не сохранять изменения<br/>и закрыть окно</span>
                </div>
                <div class="inline-block" style="vertical-align:top">
                    <input type="button" class="buttons" value="Очистить" onclick="location.reload();"/> &nbsp; 
                    <span style="font-weight:300;font-size:11px;text-align:left">сбросить все изменения</span>
                </div>
                <{*<div class="inline-block" style="vertical-align:top">
                    <input type="submit" class="buttons" value="Применить"/> &nbsp;
                    <span style="font-weight:300;font-size:11px;text-align:left">сохранить изменения</span>
                </div>*}>
                <div class="inline-block" style="vertical-align:top">
                    <input type="submit" name="save_close" class="buttons" value="Сохранить"/>
                    <span style="font-weight:300;font-size:11px;text-align:left">сохранить изменения<br/>и закрыть окно</span>
                </div>
            </td>
        </tr>    
    </table>
</form>
            
<script type="text/javascript">
    $(function() {             
        OrderProduct.init();
<{if !$item.print_id}>                
        $('#itemSearch').focus();
<{/if}>
<{if $arrPageData.close}>
        parent.window.hs.close();
<{/if}>
    });
    var OrderProduct = {
        form: $('#orderProductsForm'),
        init: function() {
            var self = this;
            $('[name="qty"], [name="price"], [name="discount_value"]', self.form).change(function() {
                $(self.form).find('.total_price').html(Math.round($(self.form).find('[name="price"]').val()) * parseInt($(self.form).find('[name="qty"]').val()) - Math.round($(self.form).find('[name="discount_value"]').val()));
            });
            $('[name="size_id"]', self.form).change(function() {
                var cost = parseInt($(this.options[this.selectedIndex]).data("cost")) || 0;
                $(self.form).find('#sizeCost').text(cost);
            });
            parent.window.hs.onDimmerClick = function() {
                return false;
            };
            if (parent.window.hs.getExpander()) {
                parent.window.hs.getExpander().reflow();
            }
            $('#itemSearch').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '/interactive/ajax.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            zone: 'admin',
                            action: 'getPrintProduct',
                            searchStr: request.term
                        }, 
                        success: function(json) {
                            response($.map(json.items, function(item) {
                                return {
                                    label: item.title,
                                    value: item.title,
                                    id: item.id,
                                    placement: item.placement,
                                    default_substrate_id: item.default_substrate_id,
                                    middle_image: item.middle_image,
                                    substrates: item.substrates,
                                    sizes: item.sizes,
                                    assortment: item.assortment
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $(self.form).find('[name="print_id"]').val(ui.item.id);
                    $(self.form).find('[name="title"]').val(ui.item.label);
                    $(self.form).find('[name="placement"]').val(ui.item.placement);
                    $(self.form).find('.productInfo').html('<div class="inline-block" style="width:220px;vertical-align:middle">'+
                                                           '<a href="/admin.php?module=prints&task=editItem&itemID='+ui.item.id+'" target="_blank">'+
                                                            ui.item.label+'</a></div> &nbsp;&nbsp; <a href="javascript:;" '+
                                                            'class="inline-block" style="width:20px;vertical-align:middle" onclick="OrderProduct.clearProduct();">'+
                                                            '<img src="<{$arrPageData.system_images}>delete.png"/></a>');
                    $(self.form).find('.productSearch').addClass('hidden_block');
                    $(self.form).find('.productData').removeClass('hidden_block');                    
                    OrderProduct.fillProduct(ui.item);
                    parent.window.hs.getExpander().reflow();
                    $(this).val("");
                    return false;
                },
                minLength: 2
            });
        },
        getSubstrate: function(substrateID) {
            var self = this;
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'getPrintProduct',
                    substrateID: substrateID,
                    itemID: $(self.form).find('[name="print_id"]').val()
                }, 
                success: function(json) {
                    if(json.items) {
                        OrderProduct.fillProduct(json.items[0]);
                    }
                }
            });
        },
        fillProduct: function(item) {
            var self = this;
            OrderProduct.setPreview(item.middle_image);
            //fill substrates
            if(item.substrates) {
                var substrates = $(self.form).find('[name="substrate_id"]');
                var html = '';
                for(var i=0; i<item.substrates.length; i++) {
                    html+= '<option value="'+item.substrates[i].substrate_id+'" data-price="'+item.substrates[i].price+'" '+(item.default_substrate_id == item.substrates[i].substrate_id ? ' selected' : '')+'>'+
                            item.substrates[i].substrate_title+' ('+item.substrates[i].price+' грн)</option>';
                }
                $(substrates).find('option:not(.default)').remove();
                $(substrates).find('option.default').addClass('hidden_block');
                $(substrates).append(html);
            }
            //fill colors
            if(item.assortment) {
                var colors = $(self.form).find('[name="color_id"]');
                var html = '';
                for(var i=0; i<item.assortment.length; i++) {
                    html+= '<option value="'+item.assortment[i].color_id+'" '+
                                    'data-image="'+item.assortment[i].middle_image+'"'+(item.assortment[i].is_default == 1 ? ' selected' : '')+'>'+
                            item.assortment[i].color_title+'</option>';
                }
                $(colors).find('option:not(.default)').remove();
                $(colors).find('option.default').addClass('hidden_block');
                $(colors).append(html);
            }
            //fill sizes
            if(item.sizes) {
                var sizes = $(self.form).find('[name="size_id"]');
                var html = '';
                for(var i=0; i<item.sizes.length; i++) {
                    html+= '<option value="'+item.sizes[i].id+'" data-cost="'+item.sizes[i].cost+'">'+item.sizes[i].title+'</option>';
                }
                $(sizes).find('option:not(.default)').remove();
                $(sizes).find('option.default').addClass('hidden_block');
                $(sizes).append(html);
            }
            //fill price and cnt
            if($('[name="qty"]').val().length==0) $('[name="qty"]').val(1);
            $('[name="price"]').val($(self.form).find('[name="substrate_id"]').find('option:selected').data('price'));
            $('[name="price"]').change();
        },  
        clearProduct: function() {
            var self = this;
            if(confirm("Открепить выбранный товар?")) {
                $(self.form).find('[name="print_id"]').val('');
                $(self.form).find('[name="title"]').val('');
                $(self.form).find('[name="placement"]').val('');
                $(self.form).find('.productInfo').html('');
                $(self.form).find('.productSearch').removeClass('hidden_block');
                $(self.form).find('.productData').addClass('hidden_block');
                OrderProduct.setPreview();
            }
        },
        setPreview: function(image) {
            var self  = this,
                image = (typeof image != "undefined" ? image : '/uploaded/prints/noimage.jpg');
            $(self.form).find('.productPreview').find('img').attr('src', image);
        },
        checkForm: function() {
            var self = this,
                errors = 0;
            $.each($(self.form).find('.required'), function() {
                if ($(this).val().length == 0 || $(this).val() == 0) {
                    $(this).addClass('error');
                    errors++;
                    if ($(this).attr('name') == 'print_id') {
                        $('#itemSearch').addClass('error');
                    }
                } else {
                    $(this).removeClass('error');
                    if ($(this).attr('name') == 'print_id') {
                        $('#itemSearch').removeClass('error');
                    }
                }
            }); return (errors > 0 ? false : true);
        }
    };
</script>