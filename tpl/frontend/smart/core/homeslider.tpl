<div class="banner homeslider">
    <div class="swiper-container">
        <div class="swiper-wrapper">
<{foreach from=$HTMLHelper->getSliderItems() item=arItem name=i}>
            <div class="swiper-slide">
<{if !empty($arItem.url)}>
                <a href="<{$arItem.url}>">
<{/if}>
                    <img src="<{$arItem.path}><{$arItem.image}>" alt="<{$arItem.title}>">
<{if !empty($arItem.url)}>
                </a>
<{/if}>
            </div>
<{/foreach}>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
    </div>
</div>