<div class="product-form horizontal">
<{if !empty($item.assortment)}>
    <div class="select-color">
        <label class="h-label">Выберите цвет</label>
        <div class="row">
<{foreach from=$item.assortment item=assort}>
            <input
                type="radio"
                name="color" 
                class="hidden"
                value="<{$assort.color_id}>"
                id="color_<{$assort.color_id}>"
                data-options='<{$assort.sizes|json_encode}>'
                data-img-src="<{$assort.big_image}>"
                data-color-id="<{$assort.color_id}>"
                data-color-hex="<{$assort.color_hex}>"
                <{if $assort.is_default}>checked<{/if}>>
            <label class="check-label" for="color_<{$assort.color_id}>" title="<{$assort.color_title}>">
                <div class="pic" style="background-color: #<{$assort.color_hex}>;"></div>
                <span><{$HTMLHelper->shortenColorTitle($assort.color_title, 8)}></span>
            </label>
<{/foreach}>
        </div>
    </div>
<{/if}>
<{if !empty($item.sizes)}>
    <div class="select-size">
        <label class="h-label">Выберите размер</label>
        <div class="size-list">
<{foreach from=$item.sizes item=size}>
            <input type="radio" name="size" id="size_<{$size.id}>" value="<{$size.id}>" data-cost="<{$size.cost}>" <{if $item.size_id==$size.id}>checked<{/if}>>
            <label for="size_<{$size.id}>" class="check-label"><{$size.title}></label>
<{/foreach}>
        </div>
    </div>
<{/if}>
</div>