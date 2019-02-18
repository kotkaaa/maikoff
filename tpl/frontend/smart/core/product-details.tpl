<{include file="core/product-price.tpl"}>
<{include file="core/product-options.tpl"}>
<{include file="core/product-print-types.tpl"}>
<label class="switch-toggle">Доставка<{if $arCategory.module=="prints"}> и оплата<{/if}></label>
<div class="delivery switch-content">
    <ul>
        <li>
            <p>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.delivery}>" target="_blank" rel="nofollow">По Киеву</a>
            </p>
            <p>
                Бесплатная доставка при заказе 4-х и более единиц товара с печатью.
                Стоимость доставки для товаров без печати
                и при заказе менее 4-х единиц с печатью от 55 грн.
            </p>
        </li>
        <li>
            <p>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.delivery}>" target="_blank" rel="nofollow">По Украине</a>
            </p>
            <p>
                Стоимость доставки согласно тарифов Новой почты.
                Сроки доставки 1-2 рабочих дня.
            </p>
        </li>
        <li>
            <p>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.delivery}>" target="_blank" rel="nofollow">Самовывоз в Киеве</a>
            </p>
            <p>
                Вы можете забрать<{if $arCategory.module=="prints"}> и оплатить<{/if}> свой заказ y нас в офисе
                с 10 до 19 в рабочие дни.
                Удобное местоположение – Подол (м. Тараса Шевченко).
                Звоните и приезжайте!
            </p>
        </li>
    </ul>
</div>
<{if !empty($item.size_grid)}>
<label class="switch-toggle size-table-toggle">Таблица размеров</label>
<div class="size-table-wrap switch-content">
    <{include file="core/size-table.tpl"}>
</div>
<{/if}>
<label class="switch-toggle">Описание товара</label>
<div class="description switch-content">
    <table class="product-attributes">
<{if !empty($item.brand)}>
        <tr>
            <td class="thin">Бренд</td>
            <td>
                <a href="<{include file="core/href_item.tpl" arCategory=$arrModules.brands arItem=$item.brand params=""}>" class="bold"><{$item.brand.title}></a>
            </td>
        </tr>
<{/if}>
<{foreach name=i from=$item.attributes item=arItem}>
        <tr>
            <td class="thin"><{$arItem.title}></td>
            <td class="bold">
<{foreach name=j from=$arItem.values item=arValue}>
<{if !empty($arValue.image)}>
                <img width="24" alt="<{$arValue.title}>" src="<{$arItem.image}>"/>
<{/if}>
                <{$arValue.title}>
<{if !$smarty.foreach.j.last}>, <{/if}>
<{/foreach}>
            </td>
        </tr>
<{/foreach}>
    </table>
    <{if $item.text}><{$item.text}><{/if}>
    <{include file="core/product-social.tpl"}>
    <{include file="core/product-article.tpl" article=$item.article}>
</div>