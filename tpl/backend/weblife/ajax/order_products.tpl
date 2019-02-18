<table id="orderProducts" width="100%" border="1" cellspacing="1" cellpadding="0" class="list colored">
    <tr>
        <td id="headb" width="50">Артикул</td>
        <td id="headb" width="50"></td>
        <td id="headb" align="left" width="200">Товар</td> 
        <td id="headb" align="left">Коммент</td>
        <td id="headb" align="center" width="38">Кол-во</td>
        <td id="headb" align="center" width="50">Цена за шт, грн</td>
        <td id="headb" align="center" width="50">Сумма, грн</td>
        <td id="headb" align="center" width="50">Скидка, грн</td>
        <td id="headb" align="center" width="50">Итого, грн</td>
        <{if $arrPageData.isEditableProducts}>
        <td id="headb" align="center" width="38">Ред</td>
        <td id="headb" align="center" width="38">Удал</td>
        <{/if}>
    </tr>
<{section name=i loop=$item.arProducts}>
    <tr style="background:<{OrderHelper::getProductBG($item.arProducts[i])}>">
        <td align="center"><{$item.arProducts[i].pcode}></td>
        <td align="center">
            <{if $item.arProducts[i].is_cuted || $item.arProducts[i].is_printed}>
            <div class="badges">
                <div class="badge"><{if $item.arProducts[i].is_printed}>напечатано<{else if $item.arProducts[i].is_cuted}>порезано<{/if}></div>
            <{/if}>
                <a href="<{$item.arProducts[i].product_image}>" class="highslide" onclick="return hs.expand(this);">
                    <img src="<{$item.arProducts[i].product_image}>" width="50" style="border:none"/>
                </a>
            <{if $item.arProducts[i].is_cuted || $item.arProducts[i].is_printed}>
            </div>
            <{/if}>
        </td>
        <td align="left">
            <a href="/admin.php?module=<{$item.arProducts[i].module}>&task=editItem<{if $item.arProducts[i].model_id}>&modelID=<{$item.arProducts[i].model_id}><{/if}>&itemID=<{$item.arProducts[i].product_id}>" target="_blank"><b><{$item.arProducts[i].title|unscreenData}></b></a><br/>
            Положение: <{if $item.arProducts[i].placement}><{PrintProduct::$arSides[$item.arProducts[i].placement]}><{/if}><br/>
            Тип товара: <{$item.arProducts[i].substrate_title}><br/>
            Цвет: <{$item.arProducts[i].color_title}><{if $item.arProducts[i].color_hex}> (<{$item.arProducts[i].color_hex}>)<{/if}><br/>
            Размер: <{$item.arProducts[i].size_title}><br/>
            <{if $item.arProducts[i].brand_title}>Бренд: <{$item.arProducts[i].brand_title}><br/><{/if}>
            <{if $item.arProducts[i].series_title}>Серия: <{$item.arProducts[i].series_title}><br/><{/if}>
        </td>                                        
        <td align="left"><{$item.arProducts[i].admin_comment}></td>
        <td align="center"><{$item.arProducts[i].qty}></td>
        <td align="center"><{HTMLHelper::formatPrice($item.arProducts[i].price)}></td>
        <td align="center"><{HTMLHelper::formatPrice($item.arProducts[i].price*$item.arProducts[i].qty)}></td>
        <td align="center"><{HTMLHelper::formatPrice($item.arProducts[i].discount_value)}></td>
        <td align="center"><b><{HTMLHelper::formatPrice($item.arProducts[i].total_price)}></b></td>
        <{if $arrPageData.isEditableProducts}>
        <td align="center">
            <{if $item.arProducts[i].module == 'catalog'}>
                --
            <{else if ($item.status_id!=OrderHelper::STATUS_INDUSTRY || !$item.arProducts[i].is_printed)}>
                <a href="/admin.php?module=order_product&task=editItem&orderID=<{$item.id}>&itemID=<{$item.arProducts[i].id}>&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'Редактирование товара', objectType:'iframe', preserveContent: false, width:800})">
                    <img src="<{$arrPageData.system_images}>edit.png" />
                </a>
            <{else}> 
                --
            <{/if}>
        </td>
        <td align="center">
            <{if $item.arProducts[i].module == 'catalog'}>
                --
            <{else if ($item.status_id!=OrderHelper::STATUS_INDUSTRY || !$item.arProducts[i].is_printed)}>
                <a href="/admin.php?module=order_product&task=deleteItem&orderID=<{$item.id}>&itemID=<{$item.arProducts[i].id}>&ajax=1" onclick="return confirm('Удалить товар?');">
                    <img src="<{$arrPageData.system_images}>delete.png" />
                </a>
            <{else}> 
                --
            <{/if}>
        </td>
        <{/if}>
    </tr>
<{/section}>
    <tr>
        <td align="right" colspan="<{if $arrPageData.isEditableProducts}>9<{else}>7<{/if}>">
            <strong>Всего товаров:</strong>
        </td>
        <td align="center" colspan="2">
            <strong><{$item.total_qty}></strong>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="<{if $arrPageData.isEditableProducts}>9<{else}>7<{/if}>">
            <strong>Стоимость товаров, грн:</strong>
        </td>
        <td align="center" colspan="2">
            <strong><{HTMLHelper::formatPrice($item.total_price)}></strong>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="<{if $arrPageData.isEditableProducts}>9<{else}>7<{/if}>">
            <strong>Стоимость доставки, грн:</strong>
        </td>
        <td align="center" colspan="2">
            <strong><{if $item.shipping_price > 0}><{HTMLHelper::formatPrice($item.shipping_price)}><{else}><{$item.shipping_price_title}><{/if}></strong>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="<{if $arrPageData.isEditableProducts}>9<{else}>7<{/if}>">
            <strong>Сумма к оплате, грн:</strong>
        </td>
        <td align="center" colspan="2">
            <strong><{HTMLHelper::formatPrice($item.total_price+$item.shipping_price)}></strong>
        </td>
    </tr>        
    <{if $item.prepay>0}>
    <tr>
        <td align="right" colspan="<{if $arrPageData.isEditableProducts}>9<{else}>7<{/if}>">
            <strong>Предоплата, грн:</strong>
        </td>
        <td align="center" colspan="2">
            <strong><{HTMLHelper::formatPrice($item.prepay)}></strong>
        </td>
    </tr>    
    <tr>
        <td align="right" colspan="<{if $arrPageData.isEditableProducts}>9<{else}>7<{/if}>">
            <strong>Остаток к оплате, грн:</strong>
        </td>
        <td align="center" colspan="2">
            <strong><{HTMLHelper::formatPrice($item.total_price+$item.shipping_price-$item.prepay)}></strong>
        </td>
    </tr>    
    <{/if}>
</table>