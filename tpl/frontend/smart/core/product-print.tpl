<div class="product-item"
    id="product_<{$item.id}>_<{$item.product_id}>_<{$item.print_id}>"
    data-idkey="<{$item.idKey}>"
    data-img-src="<{$item.middle_image}>"
    data-title="<{$item.title}>"
    data-href="<{$item.product_url}><{$item.color_hash}>"
    data-color-id="<{$item.color_id}>">
    <a href="<{$item.product_url}><{$item.color_hash}>" class="product-grid-image">
        <img src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<{$item.middle_image}>" class="default lazy" alt="">
    </a>
    <p>
        <a href="<{$item.product_url}><{$item.color_hash}>" class="product-grid-name"><{$item.title}></a>
    </p>
    <p class="product-grid-price">
        <span><{$item.price|number_format:0:".":" "}></span>
    </p>
    <div class="product-wrapper">
        <a href="<{$item.product_url}><{$item.color_hash}>" class="product-grid-image">
            <img src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" data-original="<{$item.middle_image}>" class="image-2 lazy" alt="">
        </a>
        <div class="product-information">
            <p>
                <a href="<{$item.product_url}><{$item.color_hash}>" class="product-grid-name"><{$item.title}></a>
            </p>
            <p class="product-grid-price">
                <span><{$item.price|number_format:0:".":" "}></span>
            </p>
            <p class="description"><{$item.descr}></p>
<{if !empty($item.assortment)}>
            <ul>
<{foreach from=$item.assortment item=assort}>
                <li class="pic <{if $assort.is_default}>checked<{/if}>" data-color-id="<{$assort.color_id}>">
                    <label data-href="<{$item.product_url}>#<{$assort.color_hex}>" data-img-src="<{$assort.middle_image}>" onclick="window.location.assign($(this).data('href'));">
                        <span style="background-color: #<{$assort.color_hex}>;"></span>
                    </label>
                </li>
<{/foreach}>
            </ul>
<{/if}>
        </div>
    </div>
</div>