<{if !empty($arItems)}>
<div class="popular-brand">
    <h2>Популярные бренды</h2>
    <span>Мы собрали проверенных производителей за качество одежды которых 100% можем быть уверенны</span>
    <div class="popular-wrapper pop-brand-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
        </div>
        <div class="swiper-container">
            <div class="swiper-scrollbar"></div>
            <div class="swiper-wrapper">
<{foreach name=i from=$arItems item=brand}>
                <div class="swiper-slide">
<{*                    <a href="<{include file="core/href_item.tpl" arCategory=$arrModules.brands arItem=$brand params=""}>">*}>
                        <img src="<{$brand.image}>" alt="<{$brand.title}>">
<{*                    </a>*}>
                </div>
<{/foreach}>
            </div>
        </div>
    </div>
</div>
<{/if}>