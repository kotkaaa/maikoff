<{assign var=watched value=$HTMLHelper->getLastWatched($UrlWL)}>
<{if !empty($watched)}>
<div class="product-slider">
    <h2 class="h2-product-slider-1">Просмотренные товары</h2>
    <div class="product-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
        </div>
        <div class="swiper-container">
            <div class="swiper-scrollbar"></div>
            <div class="swiper-wrapper">
<{foreach from=$watched item=$watchedItem}>
                <div class="swiper-slide product-item">
                    <a href="<{include file="core/href_item.tpl" arCategory=$watchedItem.arCategory arItem=$watchedItem params=""}>">
                        <img src="<{$watchedItem.middle_image}>" alt="<{$watchedItem.title}>">
                    </a>
                    <p class="product-name-little">
                        <a href="<{include file="core/href_item.tpl" arCategory=$watchedItem.arCategory arItem=$watchedItem params=""}>"><{$watchedItem.title}></a>
                    </p>
                    <span class="product-price"><{$watchedItem.price|number_format:0:'.':' '}> </span><span class="grn">грн</span>
                </div>
<{/foreach}>
            </div>
        </div>
    </div>
</div>
<{/if}>