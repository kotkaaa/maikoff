<div class="product-image">
    <div class="product-gallery">
        <div class="screen">
            <div class="swiper-container">
                <div class="swiper-wrapper">
<{foreach name=i from=$item.assortment item=assort}>
                    <div class="swiper-slide swiper-no-swiping" data-color-id="<{$assort.color_id}>">
                        <img src="<{$assort.big_image}>" alt="<{$item.title}> цвет <{$assort.color_title}>" title="<{$item.title}> цвет <{$assort.color_title}> фото"/>
                    </div>
<{/foreach}>
                </div>
            </div>
        </div>
<{if $showThumbs and count($item.assortment)>1}>
        <div class="thumbs">
            <div class="swiper-container">
                <div class="swiper-wrapper">
<{foreach name=i from=$assortment item=assort}>
                    <div class="swiper-slide thumb <{if $assort.is_default}>selected<{/if}>" onclick="">
                        <img src="<{$assort.small_image}>" alt=""/>
                    </div>
<{/foreach}>
                </div>
            </div>
        </div>
<{/if}>
    </div>
    <{include file="core/product-social.tpl"}>
</div>