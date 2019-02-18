<div class="product-item" 
    id="product_<{$item.id}>_<{$item.product_id}>_<{$item.model_id}>"
    data-idkey="<{$item.idKey}>"
    data-img-src="<{$item.middle_image}>"
    data-title="<{$item.title}>"
    data-href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>"
    data-color-id="<{$item.color_id}>">
<{if $item.is_fast_print}>
    <div class="sticker"></div>
<{/if}>
    <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>" class="product-grid-image">
        <img src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<{$item.middle_image}>" class="default lazy" alt="">
    </a>
    <p>
        <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>" class="product-grid-name"><{$item.title}></a>
    </p>
    <p class="product-grid-price">
        <span><{$item.price|number_format:0:".":" "}></span>
    </p>
    <div class="product-wrapper">
<{if $item.is_fast_print}>
        <div class="sticker">
            <p>быстрая<br>печать</p>
        </div>
<{/if}>
        <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>" class="product-grid-image">
            <img src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<{$item.middle_image}>" class="image-2 lazy" alt="">
        </a>
        <div class="product-information">
            <p>
                <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>" class="product-grid-name"><{$item.title}></a>
            </p>
            <p class="product-grid-price">
                <span><{$item.price|number_format:0:".":" "}></span>
            </p>
            <p class="description"><{$item.descr}></p>
<{if !empty($item.colors)}>
            <ul>
<{foreach from=$item.colors item=color}>
                <li class="pic <{if $color.checked}>checked<{/if}>" data-color-id="<{$color.color_id}>">
                    <label data-href="<{include file="core/href_item.tpl" arCategory=$color.arCategory arItem=$color params=""}>" data-title="<{$color.product_title}>" data-img-src="<{$color.image}>" onclick="window.location.assign($(this).data('href'));">
                        <span style="background-color: #<{$color.hex}>;"></span>
                    </label>
                </li>
<{/foreach}>
            </ul>
<{/if}>
        </div>
    </div>
</div>