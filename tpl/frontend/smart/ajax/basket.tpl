<{foreach name=i from=$Basket->getItems() key=arKey item=arItem}>
<{*<pre><{$arItem|print_r}></pre>*}>
<div class="list-item clearfix">
    <div class="x-title">
        <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}>"><{$arItem.title}></a>
    </div>
    <div class="list-item-info clearfix">
        <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>">
            <img src="<{$arItem.small_image}>" alt="<{$arItem.title}>" class="pull-left"/>
        </a>
        <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}><{if $arItem.module=="prints"}>#<{$arItem.color_hex}><{/if}>" class="title"><strong><{$arItem.title}></strong></a><br/>
<{if !empty($arItem.color_title)}>
        <{$arItem.color_title}> цвет<br/>
<{/if}>
<{if !empty($arItem.size_title)}>
        Размер <{$arItem.size_title}><br/>
<{/if}>
        Артикул <{$arItem.pcode}>
        <div class="x-price">
            <{$arItem.price|number_format:0:".":" "}>
        </div>
    </div>
    <div class="list-item-qty">
        <div class="spin-edit">
            <button class="spin-btn <{if $arItem.quantity > 1}>spin-down<{else}>spin-del<{/if}>" onclick="<{if $arItem.quantity > 1}>Basket.add('<{$arItem.idKey}>', <{intval($arItem.quantity-1)}>, true);<{else}>Basket.del('<{$arItem.idKey}>');<{/if}>"></button>
            <input type="text" value="<{$arItem.quantity}>">
            <button class="spin-btn spin-up" onclick="Basket.add('<{$arItem.idKey}>', <{intval($arItem.quantity+1)}>, true);"></button>
        </div>
    </div>
    <div class="list-item-price sub-total">
        <{$arItem.price|number_format:0:".":" "}>
    </div>
    <div class="list-item-price total">
        <{$arItem.amount|number_format:0:".":" "}>
    </div>
</div>
<{foreachelse}>
<p style="text-align: center;">Корзина пуста!</p>
<{/foreach}>