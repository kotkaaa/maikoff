<div class="product-print-form-flex">
    <{include file="core/product-print-price.tpl"}>
    <{include file="core/product-print-options.tpl"}>
</div>
<{if !empty($item.substrates) and count($item.substrates)>1}>
<div class="product-related">
    <{*<div class="slider-nav">
        <a href="#" class="selected" data-index="0">Мужчине</a>
        <a href="#" class="" data-index="1">Женщине</a>
        <a href="#" class="" data-index="2">Ребенку</a>
    </div>*}>
    <div class="slider-tabs">
        <div class="slider-tab selected">
            <div class="slider">
                <div class="swiper-wrapper">
<{foreach from=$item.substrates item=type}>
<{if $type.selected}>
                    <div class="swiper-slide selected">
                        <img src="<{$type.small_image}>" alt="<{$type.substrate_title}>"/>
                        <div class="title"><{$type.substrate_title}></div>
                        <div class="price">
                            <{$type.price|number_format:0:'.':' '}>
                        </div>
                    </div>
<{else}>
                    <div class="swiper-slide">
                        <a href="<{include file="core/href_item.tpl" arCategory=$item.arCategory arItem=$type params=""}>">
                            <img src="<{$type.small_image}>" alt="<{$type.substrate_title}>"/>
                        </a>
                        <a href="<{include file="core/href_item.tpl" arCategory=$item.arCategory arItem=$type params=""}>" class="title"><{$type.substrate_title}></a>
                        <div class="price">
                            <{$type.price|number_format:0:'.':' '}>
                        </div>
                    </div>
<{/if}>
<{/foreach}>
                </div>
            </div>
        </div>
    </div>
</div>
<{/if}>
<br/>
<{include file="core/product-print-types.tpl"}>
<label class="switch-toggle">Доставка и оплата</label>
<div class="delivery switch-content">
    <ul>
        <li>
            <p>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.delivery}>" target="_blank" rel="nofollow">По Киеву</a>
            </p>
            <p>
                БЕСПЛАТНАЯ доставка при заказе 4-х и более единиц товара. Менее 4-x единиц – 50 грн.<br/>
                Оплата наличными при получении заказа.
            </p>
        </li>
        <li>
            <p>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.delivery}>" target="_blank" rel="nofollow">По Украине</a>
            </p>
            <p>
                БЕСПЛАТНАЯ доставка по Украине Новой Почтой при заказе 4-х и более единиц товара.<br/>
                Менее 4-x единиц – от 50 грн.<br/>
                Сроки доставки – от 1-2 дней. Оплата наложенным платежом при получении товара или предоплата на карту.
            </p>
        </li>
        <li>
            <p>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.delivery}>" target="_blank" rel="nofollow">Самовывоз в Киеве</a>
            </p>
            <p>
                Вы можете оплатить и забрать свой заказ в нашем офисе с 10 до 19 в рабочие дни.<br/>
                Удобное местоположение – Подол (м. Тараса Шевченко). Звоните и приезжайте!
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
<{if !empty($item.brand) or !empty($item.attributes) or !empty($item.text)}>
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
                <{$arValue.title}><{if !$smarty.foreach.j.last}>, <{/if}>
<{/foreach}>
            </td>
        </tr>
<{/foreach}>
    </table>
    <{if $item.text}><{$item.text}><{/if}>
    <{include file="core/product-social.tpl"}>
</div>
<{/if}>
<label class="switch-toggle desktop-hidden">Методы печати</label>
<div class="print-method switch-content">
    <div class="print-wrapper">
        <ul>
            <li>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.landing_termoperenos}>" target="_blank">Термоперенос</a> немецкими пленками PoliTape -
                срок експлуатации 50 стирок
            </li>
            <li>
                <a href="<{include file="core/href.tpl" arCategory=$arrModules.landing_pramaya_pechat}>">Прямая цифровая печать</a> по ткани -
                срок эксплуатации 30 стирок
            </li>
        </ul>
        <p>Нанесение не трескается, не отклеивается и сохраняет товарный вид при правильной эксплуатации.</p>
    </div>
</div>
<label class="switch-toggle desktop-hidden">Рекомедации по уходу</label>
<div class="care-recomendation switch-content">
    <div class="care-wrapper">
        <ul>
            <li>
                <div class="icon"></div>
                <p>Не допускать контакта поверхности утюга с изображением</p>
            </li>
            <li>
                <div class="icon"></div>
                <p>Стирка при температупе 40° в бережном режиме, не использовать отбеливатель</p>
            </li>
            <li>
                <div class="icon"></div>
                <p>При правильном уходе изделия выдерживают 50 и более стирок</p>
            </li>
        </ul>
    </div>
</div>