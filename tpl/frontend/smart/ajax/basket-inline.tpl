<{if $Basket->getTotalAmount()>0}>
<div class="order-edit">
    <p>
        Ваш заказ
    </p>
    <p>
        <a href="javascript:Basket.open();">Редактировать заказ</a>
    </p>
</div>
<{foreach name=i from=$Basket->getItems() key=arKey item=arItem}>
<div class="order-item">
    <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>">
        <img src="<{$arItem.small_image}>" alt="<{$arItem.title}>">
    </a>
    <div class="order-info">
        <p>
            <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>"><{$arItem.title}></a>
        </p>
        <ul>
<{if !empty($arItem.color_title)}>
            <li>
                <span><{$arItem.color_title}> цвет</span>
            </li>
<{/if}>
<{if !empty($arItem.size_title)}>
            <li>
                <span class="will-hide">Размер <{$arItem.size_title}></span>
            </li>
<{/if}>
            <li>
                <span>Артикул <{$arItem.pcode}></span>
            </li>
            <li>
                <span class="will-hide">Количество: <{$arItem.quantity}> шт</span>
            </li>
        </ul>
        <div class="order-price">
            <span class="price"><{$arItem.amount|number_format:0:".":" "}> </span>
        </div>
    </div>
</div>
<{/foreach}>
<div class="total-amount">
    <table>
<{if $Basket->getShippingID()!=2}>
<{if $Basket->getTotalAmount()<4 and $Basket->getShippingID()==1}>
        <tr>
            <td>Сумма</td>
            <td><{$Basket->getTotalPrice()|number_format:0:'.':' '}></td>
        </tr>
        <tr>
            <td>Стоимость доставки</td>
            <td><{$Basket->getShippingPrice()|number_format:0:'.':' '}></td>
        </tr>
<{elseif $Basket->getTotalAmount()>=4 and $Basket->getShippingID()==1}>
        <tr>
            <td>Сумма</td>
            <td><{$Basket->getTotalPrice()|number_format:0:'.':' '}></td>
        </tr>
        <tr>
            <td>Стоимость доставки</td>
            <td class="free">Бесплатно</td>
        </tr>
<{/if}>
<{/if}>
        <tr>
            <td class="large">Общая сумма</td>
            <td class="large"><{$Basket->getTotalPrice(1)|number_format:0:'.':' '}></td>
        </tr>
<{if $Basket->getShippingID()==2}>
        <tr>
            <td class="small" colspan="2">Стоимость доставки<br>
            согласно тарифов компании-перевозчика</td>
        </tr>
<{/if}>
    </table>
</div>
<{else}>
<script type="text/javascript">
    window.location.reload();
</script>
<{/if}>